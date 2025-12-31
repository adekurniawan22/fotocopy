<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Validator, Auth};
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\{
    HistoryTool,
    Spki,
    Organization,
    User,
    Tool,
    MethodTool,
    Option,
    Role
};


class SpkiController extends Controller
{
    private const OPTION_ID = 2;

    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $limit = $request->input('limit', 10);

        $status = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Spki::with(['organization', 'penanggungJawab', 'approver']);

        $currentRole = session('user_data.role_id');
        $currentOrgId = session('user_data.organization_id');

        if ($currentRole != 1) {
            $query->where('organization_id', $currentOrgId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($startDate) {
            $query->whereDate('mulai_pelaksanaan', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('mulai_pelaksanaan', '<=', $endDate);
        }

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor_spki', 'like', '%' . $keyword . '%')
                    ->orWhere('macam_pekerjaan', 'like', '%' . $keyword . '%')
                    ->orWhere('lokasi_pekerjaan', 'like', '%' . $keyword . '%')
                    ->orWhere('penanggung_jawab_nama', 'like', '%' . $keyword . '%');
            });
        }

        $query->orderBy('created_at', 'desc');
        $spkis = $query->paginate($limit);

        $spkis->appends($request->only(['keyword', 'limit', 'status', 'start_date', 'end_date']));

        if ($request->ajax()) {
            return view('spki.partials.table_data', compact('spkis'));
        }

        $organizations = ($currentRole == 1) ? Organization::orderBy('organization_name', 'asc')->get() : [];

        return view('spki.list', compact('spkis', 'keyword', 'organizations', 'status', 'startDate', 'endDate'));
    }

    public function create()
    {
        $currentRole = session('user_data.role_id');
        $currentOrgId = session('user_data.organization_id');

        $data = [];

        $bulanRomawi = ["", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"];
        $bln = $bulanRomawi[date('n')];
        $thn = date('Y');

        $lastSpki = Spki::whereYear('created_at', $thn)->orderBy('spki_id', 'desc')->first();
        $lastNo = $lastSpki ? intval(substr($lastSpki->spki_id, 0, 3)) : 0;
        $newNo = str_pad($lastNo + 1, 3, '0', STR_PAD_LEFT);

        $data['generated_no'] = "NO.$newNo/PDKB-TT/$bln/$thn";

        if ($currentRole == 1) {
            $data['organizations'] = Organization::orderBy('organization_name', 'asc')->get();
            $data['users'] = [];
            $data['tools'] = [];
            $data['methods'] = [];
        } else {
            $data['organizations'] = [];
            $data['users'] = User::with('role')
                ->where('organization_id', $currentOrgId)
                ->where('is_active', 1)
                ->get();

            $data['tools'] = Tool::where('organization_id', $currentOrgId)->get();
            $data['methods'] = MethodTool::where('organization_id', $currentOrgId)->get();
        }

        return view('spki.add', $data);
    }

    private function getSigner($roleId, $orgId, $currentUserId)
    {
        if (in_array($roleId, [1, 2])) {
            $manager = User::where('organization_id', $orgId)
                ->where('role_id', 3)
                ->first();

            return $manager;
        }

        return User::find($currentUserId);
    }

    public function store(Request $request)
    {
        $rules = [
            'nomor_spki'        => 'required|unique:spki,nomor_spki',
            'dari'              => 'required|string',
            'kepada'            => 'required|exists:users,user_id',
            'macam_pekerjaan'   => 'required|string',
            'lokasi_pekerjaan'  => 'required|string',
            'mulai_pelaksanaan' => 'required|date',
            'selesai_pelaksanaan' => 'required|date|after_or_equal:mulai_pelaksanaan',
            'uraian_pekerjaan'  => 'required|string',
            'pelaksana'         => 'required|array|min:1',
            'peralatan'         => 'required|array|min:1',
        ];

        if (session('user_data.role_id') == 1) {
            $rules['organization_id'] = 'required|exists:organizations,organization_id';
        }

        foreach (['penanggung_jawab', 'pengawas_pekerjaan', 'pengawas_k3'] as $prefix) {
            $source = $request->input($prefix . '_source');
            if ($source === 'select') {
                $rules[$prefix . '_id'] = 'required|exists:users,user_id';
            } else {
                $rules[$prefix . '_nama_manual'] = 'required|string';
            }
        }

        $messages = [
            'required'       => ':attribute wajib diisi.',
            'string'         => ':attribute harus berupa teks.',
            'date'           => ':attribute harus berupa tanggal yang valid.',
            'unique'         => ':attribute sudah terdaftar di sistem.',
            'exists'         => ':attribute yang dipilih tidak valid.',
            'array'          => ':attribute harus berupa data list.',
            'min'            => ':attribute minimal harus memiliki :min item.',
            'after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',

            'kepada.exists'      => 'Penerima perintah tidak valid.',
            'pelaksana.required' => 'Minimal harus ada 1 pelaksana.',
            'peralatan.required' => 'Minimal harus ada 1 alat kerja.',
            'pelaksana.min'      => 'Minimal harus ada 1 pelaksana.',
            'peralatan.min'      => 'Minimal harus ada 1 alat kerja.',
        ];

        $attributes = [
            'nomor_spki'          => 'Nomor SPKI',
            'dari'                => 'Pemberi Perintah',
            'kepada'              => 'Penerima Perintah',
            'macam_pekerjaan'     => 'Macam Pekerjaan',
            'lokasi_pekerjaan'    => 'Lokasi Pekerjaan',
            'mulai_pelaksanaan'   => 'Tanggal Mulai',
            'selesai_pelaksanaan' => 'Tanggal Selesai',
            'uraian_pekerjaan'    => 'Uraian Pekerjaan',
            'organization_id'     => 'Organisasi',

            'penanggung_jawab_id'            => 'Penanggung Jawab',
            'penanggung_jawab_nama_manual'   => 'Nama Penanggung Jawab',
            'pengawas_pekerjaan_id'          => 'Pengawas Pekerjaan',
            'pengawas_pekerjaan_nama_manual' => 'Nama Pengawas Pekerjaan',
            'pengawas_k3_id'                 => 'Pengawas K3',
            'pengawas_k3_nama_manual'        => 'Nama Pengawas K3',
        ];

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        $validator->after(function ($validator) use ($request) {
            if ($request->pelaksana) {
                foreach ($request->pelaksana as $key => $p) {
                    $isManual = isset($p['toggle_type']) || (!isset($p['user_id']) && isset($p['nama_manual']));

                    if (empty($p['user_id']) && empty($p['nama_manual'])) {
                        $validator->errors()->add("pelaksana.$key.nama", "Baris pelaksana ke-" . ($key + 1) . " wajib diisi namanya.");
                    }
                }
            }

            if ($request->peralatan) {
                $requestedTools = [];

                foreach ($request->peralatan as $key => $t) {
                    $tId = $t['tool_id'] ?? null;
                    $qty = (int) ($t['jumlah'] ?? 0);

                    if (!$tId) {
                        if (empty($t['nama_manual']) && empty($t['tool_id'])) {
                        }
                        continue;
                    }

                    if (!isset($requestedTools[$tId])) $requestedTools[$tId] = 0;
                    $requestedTools[$tId] += $qty;
                }

                if (!empty($requestedTools)) {
                    $orgId = $request->organization_id ?? session('user_data.organization_id');

                    $masterTools = Tool::whereIn('tool_id', array_keys($requestedTools))->get()->keyBy('tool_id');

                    $activeSpkis = Spki::where('organization_id', $orgId)
                        ->where('status', 'APPROVED')
                        ->get(['peralatan']);

                    $borrowedCounts = [];
                    foreach ($activeSpkis as $spki) {
                        if (is_array($spki->peralatan)) {
                            foreach ($spki->peralatan as $item) {
                                $tId = $item['tool_id'] ?? null;
                                $qty = (int) ($item['jumlah'] ?? 0);
                                if ($tId) {
                                    if (!isset($borrowedCounts[$tId])) $borrowedCounts[$tId] = 0;
                                    $borrowedCounts[$tId] += $qty;
                                }
                            }
                        }
                    }

                    foreach ($requestedTools as $id => $reqQty) {
                        $tool = $masterTools[$id] ?? null;
                        if ($tool) {
                            $currentUsed = $borrowedCounts[$id] ?? 0;
                            $available = max(0, $tool->jumlah - $currentUsed);

                            if ($reqQty > $available) {
                                $validator->errors()->add('peralatan', "Stok alat '{$tool->nama}' tidak cukup. Diminta: {$reqQty}, Tersedia: {$available}.");
                            }
                        }
                    }
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $currentUserId = session('user_data.user_id');
            $currentRoleId = session('user_data.role_id');
            $orgId = $request->organization_id ?? session('user_data.organization_id');

            $status = 'DRAFT';
            $approvedBy = null;
            $approvedAt = null;

            $signerUser = null;
            $shouldApprove = false;

            if (in_array($currentRoleId, [1, 2])) {
                $signerUser = $this->getSigner($currentRoleId, $orgId, $currentUserId);

                if (!$signerUser) {
                    throw new \Exception("Gagal: Tidak ditemukan User dengan Role Assitant Manager di organisasi ini untuk melakukan tanda tangan.");
                }

                $shouldApprove = true;
            } else {
                if (($currentUserId == $request->kepada) || ($currentRoleId == 3)) {
                    $signerUser = User::find($currentUserId);
                    $shouldApprove = true;
                }
            }

            if ($shouldApprove && $signerUser) {
                if (empty($signerUser->signature)) {
                    $roleName = "Anda";

                    if (in_array($currentRoleId, [1, 2])) {
                        $role3 = Role::find(3);
                        $roleName = $role3 ? $role3->role_name : 'Manager';
                    }

                    throw new \Exception("Gagal Approve: {$roleName} belum mengatur tanda tangan di Profil.");
                }

                $status = 'APPROVED';
                $approvedBy = $signerUser->user_id;
                $approvedAt = Carbon::now();
            }

            $pjData = $this->resolvePersonil($request, 'penanggung_jawab');
            $ppData = $this->resolvePersonil($request, 'pengawas_pekerjaan');
            $pk3Data = $this->resolvePersonil($request, 'pengawas_k3');

            $finalTools = [];
            foreach ($request->peralatan as $t) {
                $tId = $t['tool_id'] ?? null;
                $tNama = null;
                $tSatuan = '-';

                if ($tId) {
                    $master = Tool::find($tId);
                    $tNama = $master ? $master->nama : 'Unknown Tool';
                    $tSatuan = $master ? $master->satuan : '-';
                } else {
                    $tNama = $t['nama_manual'] ?? 'Alat Manual';
                }

                $finalTools[] = [
                    'id' => $tId,
                    'nama'    => $tNama,
                    'satuan'  => $t['satuan'] ?? $tSatuan,
                    'qty'  => $t['jumlah']
                ];
            }

            $finalPelaksana = [];
            foreach ($request->pelaksana as $p) {
                $uId = $p['user_id'] ?? null;
                $uNama = null;

                if ($uId) {
                    $user = User::find($uId);
                    $uNama = $user ? $user->name : '-';
                } else {
                    $uNama = $p['nama_manual'] ?? '-';
                }

                $finalPelaksana[] = [
                    'user_id' => $uId,
                    'nama'    => $uNama
                ];
            }

            $data = [
                'organization_id' => $orgId,
                'nomor_spki'      => $request->nomor_spki,
                'dari'            => $request->dari,
                'kepada'          => $request->kepada,
                'macam_pekerjaan' => $request->macam_pekerjaan,
                'lokasi_pekerjaan' => $request->lokasi_pekerjaan,
                'mulai_pelaksanaan'   => $request->mulai_pelaksanaan,
                'selesai_pelaksanaan' => $request->selesai_pelaksanaan,
                'penanggung_jawab_id'   => $pjData['id'],
                'penanggung_jawab_nama' => $pjData['nama'],
                'pengawas_pekerjaan_id' => $ppData['id'],
                'pengawas_pekerjaan_nama' => $ppData['nama'],
                'pengawas_k3_id'        => $pk3Data['id'],
                'pengawas_k3_nama'      => $pk3Data['nama'],
                'pelaksana' => $finalPelaksana,
                'peralatan' => $finalTools,
                'uraian_pekerjaan' => $request->uraian_pekerjaan,
                'kendaraan'        => $request->kendaraan,
                'catatan'          => $request->catatan,
                'status'      => $status,
                'approved_by' => $approvedBy,
                'approved_at' => $approvedAt,
                'created_by'  => $currentUserId,
                'revision_note' => null
            ];

            $spki = Spki::create($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'SPKI Berhasil diterbitkan dengan status: ' . $status,
                'redirect' => route(session('user_data.short_role_name') . '.spki.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $spki = Spki::with(['organization', 'creator', 'penerima', 'penanggungJawab', 'approver'])->findOrFail($id);

        $option = Option::find(self::OPTION_ID);
        $allSettings = $option ? $option->text_value : [];
        $pdfSettings = $allSettings[$spki->organization_id] ?? [];

        return view('spki.partials.detail', compact('spki', 'pdfSettings'));
    }

    public function edit($id)
    {
        $spki = Spki::with(['organization', 'penerima', 'penanggungJawab', 'pengawasPekerjaan', 'pengawasK3'])
            ->findOrFail($id);

        $userId = session('user_data.user_id');
        $roleId = session('user_data.role_id');

        $isAuthorizedRole = in_array($roleId, [1, 2, 3]);
        $isOwnerDraft     = ($spki->created_by == $userId && ($spki->status == 'DRAFT' || $spki->status == 'REVISION'));

        if (!$isAuthorizedRole && !$isOwnerDraft) {
            return redirect()->route(session('user_data.short_role_name') . '.spki.index')
                ->with('error', 'Anda tidak memiliki izin mengedit dokumen ini.');
        }

        $currentOrgId = ($roleId == 1) ? $spki->organization_id : session('user_data.organization_id');

        $data = [
            'spki' => $spki,
            'organizations' => [],
            'users' => [],
            'tools' => [],
            'methods' => []
        ];

        if ($roleId == 1) {
            $data['organizations'] = Organization::orderBy('organization_name', 'asc')->get();
        } else {
            $data['users'] = User::with('role')
                ->where('organization_id', $currentOrgId)
                ->where('is_active', 1)
                ->whereNotIn('role_id', [1, 2])
                ->get();

            $data['tools'] = Tool::where('organization_id', $currentOrgId)->get();
            $data['methods'] = MethodTool::where('organization_id', $currentOrgId)->get();
        }


        return view('spki.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $spki = Spki::findOrFail($id);

        $rules = [
            'nomor_spki'        => 'required|unique:spki,nomor_spki,' . $id . ',spki_id',
            'dari'              => 'required|string',
            'kepada'            => 'required|exists:users,user_id',
            'macam_pekerjaan'   => 'required|string',
            'lokasi_pekerjaan'  => 'required|string',
            'mulai_pelaksanaan' => 'required|date',
            'selesai_pelaksanaan' => 'required|date|after_or_equal:mulai_pelaksanaan',
            'uraian_pekerjaan'  => 'required|string',
            'pelaksana'         => 'required|array|min:1',
            'peralatan'         => 'required|array|min:1',
        ];

        if (session('user_data.role_id') == 1) {
            $rules['organization_id'] = 'required|exists:organizations,organization_id';
        }

        foreach (['penanggung_jawab', 'pengawas_pekerjaan', 'pengawas_k3'] as $prefix) {
            $source = $request->input($prefix . '_source');
            if ($source === 'select') {
                $rules[$prefix . '_id'] = 'required|exists:users,user_id';
            } else {
                $rules[$prefix . '_nama_manual'] = 'required|string';
            }
        }

        $messages = [
            'required'       => ':attribute wajib diisi.',
            'string'         => ':attribute harus berupa teks.',
            'date'           => ':attribute harus berupa tanggal yang valid.',
            'unique'         => ':attribute sudah terdaftar di sistem.',
            'exists'         => ':attribute yang dipilih tidak valid.',
            'array'          => ':attribute harus berupa data list.',
            'min'            => ':attribute minimal harus memiliki :min item.',
            'after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',

            'kepada.exists'      => 'Penerima perintah tidak valid.',
            'pelaksana.required' => 'Minimal harus ada 1 pelaksana.',
            'peralatan.required' => 'Minimal harus ada 1 alat kerja.',
            'pelaksana.min'      => 'Minimal harus ada 1 pelaksana.',
            'peralatan.min'      => 'Minimal harus ada 1 alat kerja.',
        ];

        $attributes = [
            'nomor_spki'          => 'Nomor SPKI',
            'dari'                => 'Pemberi Perintah',
            'kepada'              => 'Penerima Perintah',
            'macam_pekerjaan'     => 'Macam Pekerjaan',
            'lokasi_pekerjaan'    => 'Lokasi Pekerjaan',
            'mulai_pelaksanaan'   => 'Tanggal Mulai',
            'selesai_pelaksanaan' => 'Tanggal Selesai',
            'uraian_pekerjaan'    => 'Uraian Pekerjaan',
            'organization_id'     => 'Organisasi',

            'penanggung_jawab_id'            => 'Penanggung Jawab',
            'penanggung_jawab_nama_manual'   => 'Nama Penanggung Jawab',
            'pengawas_pekerjaan_id'          => 'Pengawas Pekerjaan',
            'pengawas_pekerjaan_nama_manual' => 'Nama Pengawas Pekerjaan',
            'pengawas_k3_id'                 => 'Pengawas K3',
            'pengawas_k3_nama_manual'        => 'Nama Pengawas K3',
        ];

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        $validator->after(function ($validator) use ($request, $spki, $id) {
            if ($request->pelaksana) {
                foreach ($request->pelaksana as $key => $p) {
                    if (empty($p['user_id']) && empty($p['nama_manual'])) {
                        $validator->errors()->add("pelaksana.$key.nama", "Nama pelaksana wajib diisi.");
                    }
                }
            }

            if ($request->peralatan) {
                $requestedTools = [];
                foreach ($request->peralatan as $key => $t) {
                    $tId = $t['tool_id'] ?? null;
                    $qty = (int) ($t['jumlah'] ?? 0);

                    if (!$tId) continue;

                    if (!isset($requestedTools[$tId])) $requestedTools[$tId] = 0;
                    $requestedTools[$tId] += $qty;
                }

                if (!empty($requestedTools)) {
                    $orgId = $request->organization_id ?? $spki->organization_id;
                    $masterTools = Tool::whereIn('tool_id', array_keys($requestedTools))->get()->keyBy('tool_id');

                    $activeSpkis = Spki::where('organization_id', $orgId)
                        ->where('status', 'APPROVED')
                        ->where('spki_id', '!=', $id)
                        ->get(['peralatan']);

                    $borrowedCounts = [];
                    foreach ($activeSpkis as $itemSpki) {
                        if (is_array($itemSpki->peralatan)) {
                            foreach ($itemSpki->peralatan as $item) {
                                $tId = $item['tool_id'] ?? null;
                                $qty = (int) ($item['jumlah'] ?? 0);
                                if ($tId) {
                                    if (!isset($borrowedCounts[$tId])) $borrowedCounts[$tId] = 0;
                                    $borrowedCounts[$tId] += $qty;
                                }
                            }
                        }
                    }

                    foreach ($requestedTools as $idTool => $reqQty) {
                        $tool = $masterTools[$idTool] ?? null;
                        if ($tool) {
                            $currentUsed = $borrowedCounts[$idTool] ?? 0;
                            $available = max(0, $tool->jumlah - $currentUsed);

                            if ($reqQty > $available) {
                                $validator->errors()->add('peralatan', "Stok alat '{$tool->nama}' tidak cukup. Diminta: {$reqQty}, Tersedia: {$available}.");
                            }
                        }
                    }
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $orgId = $request->organization_id ?? $spki->organization_id;

            $pjData = $this->resolvePersonil($request, 'penanggung_jawab');
            $ppData = $this->resolvePersonil($request, 'pengawas_pekerjaan');
            $pk3Data = $this->resolvePersonil($request, 'pengawas_k3');

            $finalTools = [];
            foreach ($request->peralatan as $t) {
                $tId = $t['tool_id'] ?? null;
                $tNama = null;
                $tSatuan = '-';

                if ($tId) {
                    $master = Tool::find($tId);
                    $tNama = $master ? $master->nama : 'Unknown Tool';
                    $tSatuan = $master ? $master->satuan : '-';
                } else {
                    $tNama = $t['nama_manual'] ?? 'Alat Manual';
                }

                $finalTools[] = [
                    'id'      => $tId,
                    'nama'    => $tNama,
                    'satuan'  => $t['satuan'] ?? $tSatuan,
                    'qty'     => $t['jumlah'],
                ];
            }

            $finalPelaksana = [];
            foreach ($request->pelaksana as $p) {
                $uId = $p['user_id'] ?? null;
                $uNama = null;

                if ($uId) {
                    $user = User::find($uId);
                    $uNama = $user ? $user->name : '-';
                } else {
                    $uNama = $p['nama_manual'] ?? '-';
                }

                $finalPelaksana[] = [
                    'user_id' => $uId,
                    'nama'    => $uNama
                ];
            }

            $dataToUpdate = [
                'organization_id'       => $orgId,
                'nomor_spki'            => $request->nomor_spki,
                'dari'                  => $request->dari,
                'kepada'                => $request->kepada,
                'macam_pekerjaan'       => $request->macam_pekerjaan,
                'lokasi_pekerjaan'      => $request->lokasi_pekerjaan,
                'mulai_pelaksanaan'     => $request->mulai_pelaksanaan,
                'selesai_pelaksanaan'   => $request->selesai_pelaksanaan,

                'penanggung_jawab_id'      => $pjData['id'],
                'penanggung_jawab_nama'    => $pjData['nama'],
                'pengawas_pekerjaan_id'    => $ppData['id'],
                'pengawas_pekerjaan_nama'  => $ppData['nama'],
                'pengawas_k3_id'           => $pk3Data['id'],
                'pengawas_k3_nama'         => $pk3Data['nama'],

                'pelaksana'             => $finalPelaksana,
                'peralatan'             => $finalTools,

                'uraian_pekerjaan'      => $request->uraian_pekerjaan,
                'kendaraan'             => $request->kendaraan,
                'catatan'               => $request->catatan,
            ];

            if ($spki->status == 'REVISION') {
                $dataToUpdate['status'] = 'DRAFT';
                $dataToUpdate['revision_note'] = null;
            }

            $spki->update($dataToUpdate);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'SPKI Berhasil diperbarui.',
                'redirect' => route(session('user_data.short_role_name') . '.spki.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $spki = Spki::findOrFail($id);

            $userId = session('user_data.user_id');
            $roleId = session('user_data.role_id');

            if ($roleId != 1) {
                if ($spki->created_by != $userId || $spki->status != 'DRAFT') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki izin menghapus dokumen ini.'
                    ], 403);
                }
            }

            $spki->delete();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen SPKI berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve($id)
    {
        $spki = Spki::findOrFail($id);
        $currentUserId = session('user_data.user_id');
        $currentRoleId = session('user_data.role_id');
        $orgId = session('user_data.organization_id');

        if ($spki->kepada != $currentUserId && !in_array($currentRoleId, [1, 2, 3])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $signerUser = $this->getSigner($currentRoleId, $orgId, $currentUserId);

        if (!$signerUser) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal: Tidak ditemukan User dengan Role Assitant Manager di organisasi ini untuk tanda tangan.'
            ], 422);
        }

        if (empty($signerUser->signature)) {
            $roleName = "Anda";

            if (in_array($currentRoleId, [1, 2])) {
                $role3 = Role::find(3);
                $roleName = $role3 ? $role3->role_name : 'Manager';
            }

            return response()->json([
                'success' => false,
                'message' => "Gagal Approve: {$roleName} belum mengatur tanda tangan di Profil. Silakan update profil terlebih dahulu."
            ], 422);
        }

        $spki->update([
            'status' => 'APPROVED',
            'approved_by' => $signerUser->user_id,
            'approved_at' => Carbon::now()
        ]);

        return response()->json(['success' => true, 'message' => 'SPKI Disetujui.']);
    }

    public function revision(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string'
        ]);

        $spki = Spki::findOrFail($id);

        $spki->update([
            'status' => 'REVISION',
            'revision_note' => $request->reason,
        ]);

        return response()->json(['success' => true, 'message' => 'Dokumen dikembalikan untuk revisi.']);
    }

    public function exportPdf($id)
    {
        $spki = Spki::with(['organization', 'creator'])->findOrFail($id);
        if (Auth::user()->role_id == 1) {
            $orgId = $spki->organization_id;
        } else {
            $orgId = session('user_data.organization_id');
        }

        $option = Option::find(self::OPTION_ID);
        $allSettings = $option ? $option->text_value : [];
        $pdfSettings = $allSettings[$orgId] ?? [];

        if (empty($pdfSettings)) {
            return redirect()->back()->with('error', 'Gagal export: Pengaturan PDF SPKI belum diatur untuk organisasi ini. Silahkan atur dahulu.');
        }

        $pdf = Pdf::loadView('spki.pdf', compact('spki', 'pdfSettings'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('SPKI_' . $spki->spki_id . '.pdf');
    }

    private function resolvePersonil(Request $request, $prefix)
    {
        $source = $request->input($prefix . '_source');

        if ($source == 'select') {
            $id = $request->input($prefix . '_id');
            $u = User::find($id);
            $nama = $u ? $u->name : '-';

            return ['id' => $id, 'nama' => $nama];
        } else {
            return [
                'id' => null,
                'nama' => $request->input($prefix . '_nama_manual')
            ];
        }
    }

    public function getOrganizationData(Request $request)
    {
        $orgId = $request->organization_id;

        if (!$orgId) {
            return response()->json(['users' => [], 'tools' => [], 'methods' => []]);
        }

        $methods = MethodTool::where('organization_id', $orgId)->get(['method_id', 'nama_method', 'list_tools']);
        $users = User::with('role')->where('organization_id', $orgId)->where('is_active', 1)->whereNotIn('role_id', [1, 2])->get();
        $tools = Tool::where('organization_id', $orgId)->get();

        $borrowedCounts = [];

        $activeHistories = HistoryTool::where('organization_id', $orgId)
            ->where('is_returned', 0)
            ->where('status', 'approved')
            ->get(['list_tools']);

        foreach ($activeHistories as $history) {
            $listTools = is_array($history->list_tools) ? $history->list_tools : json_decode($history->list_tools, true);

            if (!empty($listTools['tools']) && is_array($listTools['tools'])) {
                foreach ($listTools['tools'] as $item) {
                    $tId = $item['id'] ?? $item['tool_id'] ?? null;
                    $qty = $item['qty'] ?? $item['jumlah'] ?? 0;

                    if ($tId) {
                        if (!isset($borrowedCounts[$tId])) $borrowedCounts[$tId] = 0;
                        $borrowedCounts[$tId] += (int) $qty;
                    }
                }
            }
        }

        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');

        $activeSpkis = Spki::where('organization_id', $orgId)
            ->where('status', 'APPROVED')
            ->whereDate('mulai_pelaksanaan', '<=', $today)
            ->whereDate('selesai_pelaksanaan', '>=', $today)
            ->get(['peralatan']);

        foreach ($activeSpkis as $spki) {
            $peralatanList = $spki->peralatan;

            if (!empty($peralatanList) && is_array($peralatanList)) {
                foreach ($peralatanList as $item) {
                    $tId = $item['id'] ?? $item['tool_id'] ?? null;
                    $qty = $item['qty'] ?? $item['jumlah'] ?? 0;

                    if ($tId && $qty > 0) {
                        if (!isset($borrowedCounts[$tId])) $borrowedCounts[$tId] = 0;
                        $borrowedCounts[$tId] += (int) $qty;
                    }
                }
            }
        }

        $tools->transform(function ($tool) use ($borrowedCounts) {
            $totalDipinjam = $borrowedCounts[$tool->tool_id] ?? 0;
            $tool->jumlah = max(0, $tool->jumlah - $totalDipinjam);
            return $tool;
        });

        $methodTools = null;
        if ($request->method_id) {
            $selectedMethod = MethodTool::find($request->method_id);
            if ($selectedMethod) {
                $methodToolsRaw = is_array($selectedMethod->list_tools) ? $selectedMethod->list_tools : json_decode($selectedMethod->list_tools, true);

                $methodTools = collect($methodToolsRaw)->map(function ($mTool) use ($tools) {
                    $tId = $mTool['id'] ?? $mTool['tool_id'] ?? null;
                    $masterTool = $tools->firstWhere('tool_id', $tId);

                    return [
                        'tool_id'      => $tId,
                        'tool_name'    => $masterTool ? $masterTool->nama : 'Unknown',
                        'qty'          => $mTool['qty'] ?? $mTool['jumlah'] ?? 0,
                        'current_stok' => $masterTool ? $masterTool->jumlah : 0,
                        'satuan'       => $masterTool ? $masterTool->satuan : '-'
                    ];
                });
            }
        }

        return response()->json([
            'users' => $users,
            'tools' => $tools,
            'methods' => $methods,
            'method_tools' => $methodTools
        ]);
    }
}
