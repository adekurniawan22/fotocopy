<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GarduInduk;
use App\Models\Option;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GarduIndukController extends Controller
{
    private const OPTION_EWS_ID = 4;

    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $limit = $request->input('limit', 10);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = GarduInduk::with(['organization']);

        $currentRole = session('user_data.role_id');
        $currentOrgId = session('user_data.organization_id');

        if ($currentRole != 1) {
            $query->where('organization_id', $currentOrgId);
        }

        if ($startDate && $endDate) {
            $query->whereRaw("
                EXISTS (
                    SELECT 1 
                    FROM JSON_TABLE(riwayat, '$[*].anomalies[*]' COLUMNS (
                        tgl_ews DATE PATH '$.tanggal_ews',
                        status_pj VARCHAR(50) PATH '$.status_pekerjaan'
                    )) as jt
                    WHERE 
                        jt.tgl_ews BETWEEN ? AND ? 
                        AND jt.status_pj = 'Belum Dikerjakan'
                )
            ", [$startDate, $endDate]);
        }

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('gardu_induk', 'like', '%' . $keyword . '%');
                $q->orWhereRaw('JSON_SEARCH(riwayat, "one", ?, NULL, "$[*].name") IS NOT NULL', ["%{$keyword}%"]);
            });
        }

        $query->orderBy('created_at', 'desc');
        $garduInduks = $query->paginate($limit);

        $garduInduks->appends($request->only(['keyword', 'limit', 'start_date', 'end_date']));

        $option = Option::find(self::OPTION_EWS_ID);
        $ewsConfig = $option ? $option->text_value : [];

        if ($request->ajax()) {
            return view('gardu-induk.partials.table_data', compact('garduInduks'));
        }

        return view('gardu-induk.list', compact('garduInduks', 'keyword', 'ewsConfig', 'startDate', 'endDate'));
    }

    public function show($id)
    {
        $garduInduk = GarduInduk::with('organization')->findOrFail($id);
        return view('gardu-induk.partials.detail', compact('garduInduk'));
    }

    public function create()
    {
        $currentRole = session('user_data.role_id');
        $userOrgId   = session('user_data.organization_id');
        $data = [];

        if ($currentRole == 1) {
            $data['organizations'] = Organization::orderBy('organization_name', 'asc')->get();
        } else {
            $data['organizations'] = [];
        }

        $option = Option::find(self::OPTION_EWS_ID);
        $allConfigs = $option ? $option->text_value : [];

        if ($currentRole == 1) {
            $data['ews_config'] = $allConfigs;
        } else {
            $data['ews_config'] = isset($allConfigs[$userOrgId])
                ? [$userOrgId => $allConfigs[$userOrgId]]
                : [];
        }

        return view('gardu-induk.add', $data);
    }

    public function store(Request $request)
    {
        $rules = [
            'organization_id' => 'required',
            'gardu_induk'     => 'required|string|max:255',
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png|max:15360',

            'bays'            => 'required|array|min:1',
            'bays.*.name'     => 'required|string|max:255',

            'bays.*.anomalies'                   => 'required|array|min:1',
            'bays.*.anomalies.*.jenis_anomali'    => 'required|string',
            'bays.*.anomalies.*.status_pekerjaan' => 'required|string',
            'bays.*.anomalies.*.jumlah_titik'     => 'required|integer',
            'bays.*.anomalies.*.fasa'             => 'required',
            'bays.*.anomalies.*.busbar'           => 'required|string',
            'bays.*.anomalies.*.tanggal_inspeksi' => 'required|date',
            'bays.*.anomalies.*.foto'             => 'nullable|image|mimes:jpg,jpeg,png|max:15360',

            'bays.*.anomalies.*.use_ews'          => 'required|in:0,1',

            'bays.*.anomalies.*.keparahan'        => 'required',
            'bays.*.anomalies.*.geografis'        => 'nullable',
            'bays.*.anomalies.*.usia'             => 'nullable',
            'bays.*.anomalies.*.material'         => 'nullable',
            'bays.*.anomalies.*.tanggal_ews'      => 'nullable|date',
        ];

        $messages = [
            'required' => ':attribute wajib diisi.',
            'string'   => ':attribute harus berupa teks.',
            'integer'  => ':attribute harus berupa angka.',
            'date'     => ':attribute harus berupa tanggal yang valid.',
            'array'    => ':attribute format data tidak valid.',
            'min'      => ':attribute minimal harus memiliki :min data.',
            'image'    => ':attribute harus berupa file gambar.',
            'mimes'    => ':attribute harus berformat: :values.',
            'max'      => 'Ukuran :attribute tidak boleh lebih dari :max kilobyte.',
        ];

        $attributes = [
            'organization_id'                     => 'Organisasi',
            'gardu_induk'                         => 'Nama Gardu Induk',
            'foto'                                => 'Foto Gardu',
            'bays'                                => 'Daftar Bay',
            'bays.*.name'                         => 'Nama Bay',
            'bays.*.anomalies'                    => 'Daftar Anomali',
            'bays.*.anomalies.*.jenis_anomali'    => 'Jenis Anomali',
            'bays.*.anomalies.*.status_pekerjaan' => 'Status Pekerjaan',
            'bays.*.anomalies.*.jumlah_titik'     => 'Jumlah Titik',
            'bays.*.anomalies.*.fasa'             => 'Fasa',
            'bays.*.anomalies.*.busbar'           => 'Busbar',
            'bays.*.anomalies.*.tanggal_inspeksi' => 'Tanggal Inspeksi',
            'bays.*.anomalies.*.keparahan'        => 'Tingkat Keparahan',
            'bays.*.anomalies.*.geografis'        => 'Faktor Geografis',
            'bays.*.anomalies.*.usia'             => 'Faktor Usia',
            'bays.*.anomalies.*.material'         => 'Kualitas Material',
            'bays.*.anomalies.*.tanggal_ews'      => 'Target EWS',
            'bays.*.anomalies.*.foto'             => 'Foto Bukti Anomali',
        ];

        $request->validate($rules, $messages, $attributes);

        DB::beginTransaction();

        try {
            $data = $request->except(['bays', 'foto', 'avatar_remove']);

            if ($request->hasFile('foto')) {
                $data['foto'] = $request->file('foto')->store('gardu_photos', 'public');
            }

            if (session('user_data.role_id') != 1) {
                $data['organization_id'] = session('user_data.organization_id');
            }

            $baysInput = $request->input('bays', []);

            foreach ($baysInput as $bayIndex => &$bayData) {
                if (isset($bayData['anomalies']) && is_array($bayData['anomalies'])) {

                    foreach ($bayData['anomalies'] as $anomaliIndex => &$anomaliData) {
                        $useEws = isset($anomaliData['use_ews']) ? $anomaliData['use_ews'] : '0';

                        if ($useEws == '0') {
                            $anomaliData['geografis'] = null;
                            $anomaliData['usia'] = null;
                            $anomaliData['material'] = null;
                            $anomaliData['tanggal_ews'] = null;
                        }

                        $fileKey = "bays.{$bayIndex}.anomalies.{$anomaliIndex}.foto";
                        if ($request->hasFile($fileKey)) {
                            $path = $request->file($fileKey)->store('inspeksi_photos', 'public');
                            $anomaliData['foto'] = $path;
                        } else {
                            $anomaliData['foto'] = null;
                        }

                        unset($anomaliData['avatar_remove']);
                    }

                    $bayData['anomalies'] = array_values($bayData['anomalies']);
                }
            }

            $baysInput = array_values($baysInput);

            $data['riwayat'] = $baysInput;

            GarduInduk::create($data);

            DB::commit();

            return response()->json([
                'status'   => 'success',
                'message'  => 'Data Gardu Induk, Bay, dan Anomali berhasil disimpan.',
                'redirect' => route(session('user_data.short_role_name') . '.gardu-induk.index')
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $garduInduk = GarduInduk::findOrFail($id);

        if (is_null($garduInduk->riwayat)) {
            $garduInduk->riwayat = [];
        }

        $currentRole = session('user_data.role_id');
        $userOrgId   = session('user_data.organization_id');

        $data = [
            'garduInduk' => $garduInduk,
            'organizations' => ($currentRole == 1) ? Organization::orderBy('organization_name', 'asc')->get() : []
        ];

        $option = Option::find(self::OPTION_EWS_ID);
        $allConfigs = $option ? $option->text_value : [];

        if ($currentRole == 1) {
            $data['ews_config'] = $allConfigs;
        } else {
            $data['ews_config'] = isset($allConfigs[$userOrgId])
                ? [$userOrgId => $allConfigs[$userOrgId]]
                : [];
        }

        return view('gardu-induk.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'organization_id' => 'required',
            'gardu_induk'     => 'required|string|max:255',
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png|max:15360',

            'bays'            => 'required|array|min:1',
            'bays.*.name'     => 'required|string|max:255',

            'bays.*.anomalies'                   => 'required|array|min:1',
            'bays.*.anomalies.*.jenis_anomali'    => 'required|string',
            'bays.*.anomalies.*.status_pekerjaan' => 'required|string',
            'bays.*.anomalies.*.jumlah_titik'     => 'required|integer',
            'bays.*.anomalies.*.fasa'             => 'required',
            'bays.*.anomalies.*.busbar'           => 'required|string',
            'bays.*.anomalies.*.tanggal_inspeksi' => 'required|date',
            'bays.*.anomalies.*.foto'             => 'nullable|image|mimes:jpg,jpeg,png|max:15360',

            'bays.*.anomalies.*.use_ews'          => 'required|in:0,1',

            'bays.*.anomalies.*.keparahan'        => 'required',
            'bays.*.anomalies.*.geografis'        => 'nullable',
            'bays.*.anomalies.*.usia'             => 'nullable',
            'bays.*.anomalies.*.material'         => 'nullable',
            'bays.*.anomalies.*.tanggal_ews'      => 'nullable|date',
        ];

        $messages = [
            'required' => ':attribute wajib diisi.',
            'string'   => ':attribute harus berupa teks.',
            'integer'  => ':attribute harus berupa angka.',
            'date'     => ':attribute harus berupa tanggal yang valid.',
            'array'    => ':attribute format data tidak valid.',
            'min'      => ':attribute minimal harus memiliki :min data.',
            'image'    => ':attribute harus berupa file gambar.',
            'mimes'    => ':attribute harus berformat: :values.',
            'max'      => 'Ukuran :attribute tidak boleh lebih dari :max kilobyte.',
        ];

        $attributes = [
            'organization_id'                     => 'Organisasi',
            'gardu_induk'                         => 'Nama Gardu Induk',
            'foto'                                => 'Foto Gardu',
            'bays'                                => 'Daftar Bay',
            'bays.*.name'                         => 'Nama Bay',
            'bays.*.anomalies'                    => 'Daftar Anomali',
            'bays.*.anomalies.*.jenis_anomali'    => 'Jenis Anomali',
            'bays.*.anomalies.*.status_pekerjaan' => 'Status Pekerjaan',
            'bays.*.anomalies.*.jumlah_titik'     => 'Jumlah Titik',
            'bays.*.anomalies.*.fasa'             => 'Fasa',
            'bays.*.anomalies.*.busbar'           => 'Busbar',
            'bays.*.anomalies.*.tanggal_inspeksi' => 'Tanggal Inspeksi',
            'bays.*.anomalies.*.keparahan'        => 'Tingkat Keparahan',
            'bays.*.anomalies.*.geografis'        => 'Faktor Geografis',
            'bays.*.anomalies.*.usia'             => 'Faktor Usia',
            'bays.*.anomalies.*.material'         => 'Kualitas Material',
            'bays.*.anomalies.*.tanggal_ews'      => 'Target EWS',
            'bays.*.anomalies.*.foto'             => 'Foto Bukti Anomali',
        ];

        $request->validate($rules, $messages, $attributes);

        DB::beginTransaction();

        try {
            $gardu = GarduInduk::findOrFail($id);
            $oldRiwayat = $gardu->riwayat ?? [];

            $data = $request->except(['bays', 'foto', 'avatar_remove']);

            if ($request->hasFile('foto')) {
                if ($gardu->foto && Storage::disk('public')->exists($gardu->foto)) {
                    Storage::disk('public')->delete($gardu->foto);
                }
                $data['foto'] = $request->file('foto')->store('gardu_photos', 'public');
            } elseif ($request->input('avatar_remove') == '1') {
                if ($gardu->foto && Storage::disk('public')->exists($gardu->foto)) {
                    Storage::disk('public')->delete($gardu->foto);
                }
                $data['foto'] = null;
            }

            if (session('user_data.role_id') != 1) {
                $data['organization_id'] = session('user_data.organization_id');
            }

            $baysInput = $request->input('bays', []);

            foreach ($baysInput as $bayIndex => &$bayData) {
                if (isset($bayData['anomalies']) && is_array($bayData['anomalies'])) {

                    foreach ($bayData['anomalies'] as $anomaliIndex => &$anomaliData) {
                        $useEws = isset($anomaliData['use_ews']) ? $anomaliData['use_ews'] : '0';

                        if ($useEws == '0') {
                            $anomaliData['geografis'] = null;
                            $anomaliData['usia'] = null;
                            $anomaliData['material'] = null;
                            $anomaliData['tanggal_ews'] = null;
                        }

                        $oldFotoPath = null;
                        if (isset($oldRiwayat[$bayIndex]['anomalies'][$anomaliIndex]['foto'])) {
                            $oldFotoPath = $oldRiwayat[$bayIndex]['anomalies'][$anomaliIndex]['foto'];
                        }

                        $fileKey = "bays.{$bayIndex}.anomalies.{$anomaliIndex}.foto";
                        $removeFlag = $anomaliData['avatar_remove'] ?? '0';

                        if ($request->hasFile($fileKey)) {
                            if ($oldFotoPath && Storage::disk('public')->exists($oldFotoPath)) {
                                Storage::disk('public')->delete($oldFotoPath);
                            }

                            $path = $request->file($fileKey)->store('inspeksi_photos', 'public');
                            $anomaliData['foto'] = $path;
                        } elseif ($removeFlag == '1') {
                            if ($oldFotoPath && Storage::disk('public')->exists($oldFotoPath)) {
                                Storage::disk('public')->delete($oldFotoPath);
                            }
                            $anomaliData['foto'] = null;
                        } else {
                            $anomaliData['foto'] = $oldFotoPath;
                        }

                        unset($anomaliData['avatar_remove']);
                    }

                    $bayData['anomalies'] = array_values($bayData['anomalies']);
                }
            }
            $baysInput = array_values($baysInput);
            $data['riwayat'] = $baysInput;

            $gardu->update($data);

            DB::commit();

            return response()->json([
                'status'   => 'success',
                'message'  => 'Data Gardu Induk berhasil diperbarui.',
                'redirect' => route(session('user_data.short_role_name') . '.gardu-induk.index')
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $gardu = GarduInduk::findOrFail($id);

            if ($gardu->foto && Storage::disk('public')->exists($gardu->foto)) {
                Storage::disk('public')->delete($gardu->foto);
            }

            if (!empty($gardu->riwayat) && is_array($gardu->riwayat)) {
                foreach ($gardu->riwayat as $item) {
                    if (isset($item['foto']) && $item['foto']) {
                        if (Storage::disk('public')->exists($item['foto'])) {
                            Storage::disk('public')->delete($item['foto']);
                        }
                    }
                }
            }

            $gardu->delete();

            return response()->json([
                'success' => 'Data Gardu Induk dan seluruh file terkait berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $gardu = GarduInduk::findOrFail($id);

        $riwayat = $gardu->riwayat ?? [];

        $bayIndex = $request->input('bay_index');
        $anomaliIndex = $request->input('anomali_index');

        if (!isset($riwayat[$bayIndex]['anomalies'][$anomaliIndex])) {
            return response()->json(['error' => 'Data anomali tidak ditemukan'], 404);
        }

        $riwayat[$bayIndex]['anomalies'][$anomaliIndex]['status_pekerjaan'] = 'Sudah Dikerjakan';
        $riwayat[$bayIndex]['anomalies'][$anomaliIndex]['tanggal_penyelesaian'] = now()->format('Y-m-d');

        $gardu->riwayat = $riwayat;
        $gardu->save();

        return response()->json(['success' => 'Status berhasil diperbarui']);
    }
}
