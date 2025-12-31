<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jaringan;
use App\Models\Option;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class JaringanController extends Controller
{
    private const OPTION_EWS_ID = 4;

public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $limit = $request->input('limit', 10);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Jaringan::with(['organization']);

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
                $q->where('bay_line', 'like', '%' . $keyword . '%');
                $q->orWhereRaw('JSON_SEARCH(riwayat, "one", ?, NULL, "$[*].no_tower") IS NOT NULL', ["%{$keyword}%"]);
            });
        }

        $query->orderBy('created_at', 'desc');
        $jaringans = $query->paginate($limit);

        $jaringans->appends($request->only(['keyword', 'limit', 'start_date', 'end_date']));

        $option = Option::find(self::OPTION_EWS_ID);
        $ewsConfig = $option ? $option->text_value : [];

        if ($request->ajax()) {
            return view('jaringan.partials.table_data', compact('jaringans'));
        }

        return view('jaringan.list', compact('jaringans', 'keyword', 'ewsConfig', 'startDate', 'endDate'));
    }

    public function show($id)
    {
        $jaringan = Jaringan::with('organization')->findOrFail($id);
        return view('jaringan.partials.detail', compact('jaringan'));
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

        return view('jaringan.add', $data);
    }

    public function store(Request $request)
    {
        $rules = [
            'organization_id' => 'required',
            'bay_line'        => 'required|string|max:255',
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png|max:15360',

            'towers'               => 'required|array|min:1',
            'towers.*.no_tower'    => 'required|string|max:255',
            'towers.*.jenis_tower' => 'required|string|max:255',

            'towers.*.anomalies'                   => 'required|array|min:1',
            'towers.*.anomalies.*.jenis_anomali'    => 'required|string',
            'towers.*.anomalies.*.status_pekerjaan' => 'required|string',
            'towers.*.anomalies.*.jumlah_titik'     => 'required|integer',
            'towers.*.anomalies.*.fasa'             => 'required',
            'towers.*.anomalies.*.busbar'           => 'required|string',
            'towers.*.anomalies.*.tanggal_inspeksi' => 'required|date',
            'towers.*.anomalies.*.foto'             => 'nullable|image|mimes:jpg,jpeg,png|max:15360',

            'towers.*.anomalies.*.use_ews'          => 'required|in:0,1',
            'towers.*.anomalies.*.keparahan'        => 'required',
            'towers.*.anomalies.*.geografis'        => 'nullable',
            'towers.*.anomalies.*.usia'             => 'nullable',
            'towers.*.anomalies.*.material'         => 'nullable',
            'towers.*.anomalies.*.tanggal_ews'      => 'nullable|date',
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
            'bay_line'                            => 'Nama Bay Line',
            'foto'                                => 'Foto Jaringan',
            'towers'                              => 'Daftar Tower',
            'towers.*.no_tower'                   => 'Nomor Tower',
            'towers.*.jenis_tower'                => 'Jenis Tower',
            'towers.*.anomalies'                  => 'Daftar Anomali',
            'towers.*.anomalies.*.jenis_anomali'    => 'Jenis Anomali',
            'towers.*.anomalies.*.status_pekerjaan' => 'Status Pekerjaan',
            'towers.*.anomalies.*.jumlah_titik'     => 'Jumlah Titik',
            'towers.*.anomalies.*.fasa'             => 'Fasa',
            'towers.*.anomalies.*.busbar'           => 'Busbar',
            'towers.*.anomalies.*.tanggal_inspeksi' => 'Tanggal Inspeksi',
            'towers.*.anomalies.*.keparahan'        => 'Tingkat Keparahan',
            'towers.*.anomalies.*.geografis'        => 'Faktor Geografis',
            'towers.*.anomalies.*.usia'             => 'Faktor Usia',
            'towers.*.anomalies.*.material'         => 'Kualitas Material',
            'towers.*.anomalies.*.tanggal_ews'      => 'Target EWS',
            'towers.*.anomalies.*.foto'             => 'Foto Bukti Anomali',
        ];

        $request->validate($rules, $messages, $attributes);

        DB::beginTransaction();

        try {
            $data = $request->except(['towers', 'foto', 'avatar_remove']);

            if ($request->hasFile('foto')) {
                $data['foto'] = $request->file('foto')->store('jaringan_photos', 'public');
            }

            if (session('user_data.role_id') != 1) {
                $data['organization_id'] = session('user_data.organization_id');
            }

            $towersInput = $request->input('towers', []);

            foreach ($towersInput as $towerIndex => &$towerData) {
                if (isset($towerData['anomalies']) && is_array($towerData['anomalies'])) {

                    foreach ($towerData['anomalies'] as $anomaliIndex => &$anomaliData) {
                        $useEws = isset($anomaliData['use_ews']) ? $anomaliData['use_ews'] : '0';

                        if ($useEws == '0') {
                            $anomaliData['geografis'] = null;
                            $anomaliData['usia'] = null;
                            $anomaliData['material'] = null;
                            $anomaliData['tanggal_ews'] = null;
                        }

                        $fileKey = "towers.{$towerIndex}.anomalies.{$anomaliIndex}.foto";

                        if ($request->hasFile($fileKey)) {
                            $path = $request->file($fileKey)->store('inspeksi_photos', 'public');
                            $anomaliData['foto'] = $path;
                        } else {
                            $anomaliData['foto'] = null;
                        }

                        unset($anomaliData['avatar_remove']);
                    }

                    $towerData['anomalies'] = array_values($towerData['anomalies']);
                }
            }

            $towersInput = array_values($towersInput);

            $data['riwayat'] = $towersInput;

            Jaringan::create($data);

            DB::commit();

            return response()->json([
                'status'   => 'success',
                'message'  => 'Data Jaringan, Tower, dan Anomali berhasil disimpan.',
                'redirect' => route(session('user_data.short_role_name') . '.jaringan.index')
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
        $jaringan = Jaringan::findOrFail($id);

        if (is_null($jaringan->riwayat)) {
            $jaringan->riwayat = [];
        }

        $currentRole = session('user_data.role_id');
        $userOrgId   = session('user_data.organization_id');

        $data = [
            'jaringan' => $jaringan,
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

        return view('jaringan.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'organization_id' => 'required',
            'bay_line'        => 'required|string|max:255',
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png|max:15360',

            'towers'               => 'required|array|min:1',
            'towers.*.no_tower'    => 'required|string|max:255',
            'towers.*.jenis_tower' => 'required|string|max:255',

            'towers.*.anomalies'                   => 'required|array|min:1',
            'towers.*.anomalies.*.jenis_anomali'    => 'required|string',
            'towers.*.anomalies.*.status_pekerjaan' => 'required|string',
            'towers.*.anomalies.*.jumlah_titik'     => 'required|integer',
            'towers.*.anomalies.*.fasa'             => 'required',
            'towers.*.anomalies.*.busbar'           => 'required|string',
            'towers.*.anomalies.*.tanggal_inspeksi' => 'required|date',
            'towers.*.anomalies.*.foto'             => 'nullable|image|mimes:jpg,jpeg,png|max:15360',

            'towers.*.anomalies.*.use_ews'          => 'required|in:0,1',
            'towers.*.anomalies.*.keparahan'        => 'required',
            'towers.*.anomalies.*.geografis'        => 'nullable',
            'towers.*.anomalies.*.usia'             => 'nullable',
            'towers.*.anomalies.*.material'         => 'nullable',
            'towers.*.anomalies.*.tanggal_ews'      => 'nullable|date',
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
            'bay_line'                            => 'Nama Bay Line',
            'foto'                                => 'Foto Jaringan',
            'towers'                              => 'Daftar Tower',
            'towers.*.no_tower'                   => 'Nomor Tower',
            'towers.*.jenis_tower'                => 'Jenis Tower',
            'towers.*.anomalies'                  => 'Daftar Anomali',
            'towers.*.anomalies.*.jenis_anomali'    => 'Jenis Anomali',
            'towers.*.anomalies.*.status_pekerjaan' => 'Status Pekerjaan',
            'towers.*.anomalies.*.jumlah_titik'     => 'Jumlah Titik',
            'towers.*.anomalies.*.fasa'             => 'Fasa',
            'towers.*.anomalies.*.busbar'           => 'Busbar',
            'towers.*.anomalies.*.tanggal_inspeksi' => 'Tanggal Inspeksi',
            'towers.*.anomalies.*.keparahan'        => 'Tingkat Keparahan',
            'towers.*.anomalies.*.geografis'        => 'Faktor Geografis',
            'towers.*.anomalies.*.usia'             => 'Faktor Usia',
            'towers.*.anomalies.*.material'         => 'Kualitas Material',
            'towers.*.anomalies.*.tanggal_ews'      => 'Target EWS',
            'towers.*.anomalies.*.foto'             => 'Foto Bukti Anomali',
        ];

        $request->validate($rules, $messages, $attributes);

        DB::beginTransaction();

        try {
            $jaringan = Jaringan::findOrFail($id);
            $oldRiwayat = $jaringan->riwayat ?? [];

            $data = $request->except(['towers', 'foto', 'avatar_remove']);

            if ($request->hasFile('foto')) {
                if ($jaringan->foto && Storage::disk('public')->exists($jaringan->foto)) {
                    Storage::disk('public')->delete($jaringan->foto);
                }
                $data['foto'] = $request->file('foto')->store('jaringan_photos', 'public');
            } elseif ($request->input('avatar_remove') == '1') {
                if ($jaringan->foto && Storage::disk('public')->exists($jaringan->foto)) {
                    Storage::disk('public')->delete($jaringan->foto);
                }
                $data['foto'] = null;
            }

            if (session('user_data.role_id') != 1) {
                $data['organization_id'] = session('user_data.organization_id');
            }

            $towersInput = $request->input('towers', []);

            foreach ($towersInput as $towerIndex => &$towerData) {
                if (isset($towerData['anomalies']) && is_array($towerData['anomalies'])) {

                    foreach ($towerData['anomalies'] as $anomaliIndex => &$anomaliData) {
                        $useEws = isset($anomaliData['use_ews']) ? $anomaliData['use_ews'] : '0';

                        if ($useEws == '0') {
                            $anomaliData['geografis'] = null;
                            $anomaliData['usia'] = null;
                            $anomaliData['material'] = null;
                            $anomaliData['tanggal_ews'] = null;
                        }

                        $oldFotoPath = $oldRiwayat[$towerIndex]['anomalies'][$anomaliIndex]['foto'] ?? null;

                        $fileKey = "towers.{$towerIndex}.anomalies.{$anomaliIndex}.foto";
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

                    $towerData['anomalies'] = array_values($towerData['anomalies']);
                }
            }

            $towersInput = array_values($towersInput);
            $data['riwayat'] = $towersInput;

            $jaringan->update($data);

            DB::commit();

            return response()->json([
                'status'   => 'success',
                'message'  => 'Data Jaringan berhasil diperbarui.',
                'redirect' => route(session('user_data.short_role_name') . '.jaringan.index')
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
            $jaringan = Jaringan::findOrFail($id);

            if ($jaringan->foto && Storage::disk('public')->exists($jaringan->foto)) {
                Storage::disk('public')->delete($jaringan->foto);
            }

            if (!empty($jaringan->riwayat) && is_array($jaringan->riwayat)) {
                foreach ($jaringan->riwayat as $tower) {
                    if (isset($tower['anomalies']) && is_array($tower['anomalies'])) {
                        foreach ($tower['anomalies'] as $item) {
                            if (isset($item['foto']) && $item['foto']) {
                                if (Storage::disk('public')->exists($item['foto'])) {
                                    Storage::disk('public')->delete($item['foto']);
                                }
                            }
                        }
                    }
                }
            }

            $jaringan->delete();

            return response()->json([
                'success' => 'Data Jaringan dan seluruh file terkait berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $jaringan = Jaringan::findOrFail($id);

        $riwayat = $jaringan->riwayat ?? [];

        $towerIndex = $request->input('tower_index');
        $anomaliIndex = $request->input('anomali_index');

        if (!isset($riwayat[$towerIndex]['anomalies'][$anomaliIndex])) {
            return response()->json(['error' => 'Data anomali tidak ditemukan'], 404);
        }

        $riwayat[$towerIndex]['anomalies'][$anomaliIndex]['status_pekerjaan'] = 'Sudah Dikerjakan';
        $riwayat[$towerIndex]['anomalies'][$anomaliIndex]['tanggal_penyelesaian'] = now()->format('Y-m-d');

        $jaringan->riwayat = $riwayat;
        $jaringan->save();

        return response()->json(['success' => 'Status berhasil diperbarui']);
    }
}