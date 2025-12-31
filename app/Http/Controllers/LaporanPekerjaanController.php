<?php

namespace App\Http\Controllers;

use App\Models\LaporanPekerjaan;
use App\Models\User;
use App\Models\Role;
use App\Models\Option;
use App\Models\Organization;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\{DB, Validator, Auth, Storage};
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanPekerjaanController extends Controller
{
    const OPTION_ID = 3;

    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $status = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $limit = $request->input('limit', 10);

        $query = LaporanPekerjaan::with(['creator', 'approver', 'organization']);

        $currentRoleId = session('user_data.role_id');
        $currentOrgId = session('user_data.organization_id');

        if ($currentRoleId != 1) {
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
                $q->where('judul_laporan', 'like', '%' . $keyword . '%')
                    ->orWhere('lingkup_pekerjaan', 'like', '%' . $keyword . '%');
            });
        }

        $query->orderBy('created_at', 'desc');
        $laporan = $query->paginate($limit);

        $laporan->appends($request->only(['keyword', 'status', 'start_date', 'end_date', 'limit']));

        if ($request->ajax()) {
            return view('laporan-pekerjaan.partials.table_data', compact('laporan'));
        }

        return view('laporan-pekerjaan.list', compact('laporan', 'keyword', 'status', 'startDate', 'endDate'));
    }

    public function create()
    {
        $data['organizations'] = Organization::orderBy('organization_name', 'asc')->get();
        return view('laporan-pekerjaan.add', $data);
    }

    private function getSigner($roleId, $orgId, $currentUserId)
    {
        if (in_array($roleId, [1, 2])) {
            return User::where('organization_id', $orgId)
                ->where('role_id', 3)
                ->first();
        }
        return User::find($currentUserId);
    }

    public function store(Request $request)
    {
        $rules = [
            'judul_laporan'       => 'required|string|max:255',
            'dasar_pelaksanaan'   => 'required|string',
            'mulai_pelaksanaan'   => 'required|date',
            'selesai_pelaksanaan' => 'required|date|after_or_equal:mulai_pelaksanaan',
            'lingkup_pekerjaan'   => 'required|string',
            'hasil_pekerjaan'     => 'required|string',
            'lampiran'            => 'required|array|min:1',
        ];

        if (session('user_data.role_id') == 1) {
            $rules['organization_id'] = 'required|exists:organizations,organization_id';
        }

        $messages = [
            'required'                 => ':attribute wajib diisi.',
            'date'                     => ':attribute harus berupa tanggal valid.',
            'after_or_equal'           => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'lampiran.required'        => 'Minimal harus ada 1 lampiran.',
            'organization_id.required' => 'Organisasi wajib dipilih (Admin).',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        $validator->after(function ($validator) use ($request) {
            if ($request->lampiran) {
                foreach ($request->lampiran as $key => $item) {
                    if (empty($item['judul'])) {
                        $validator->errors()->add("lampiran.$key.judul", "Judul lampiran ke-" . ($key + 1) . " wajib diisi.");
                    }
                    if (empty($item['foto_sebelum'])) {
                        $validator->errors()->add("lampiran.$key.foto_sebelum", "Foto 'Sebelum' baris ke-" . ($key + 1) . " wajib diupload.");
                    }
                    if (empty($item['foto_proses'])) {
                        $validator->errors()->add("lampiran.$key.foto_proses", "Foto 'Proses' baris ke-" . ($key + 1) . " wajib diupload.");
                    }
                    if (empty($item['foto_sesudah'])) {
                        $validator->errors()->add("lampiran.$key.foto_sesudah", "Foto 'Sesudah' baris ke-" . ($key + 1) . " wajib diupload.");
                    }
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $currentUserId = session('user_data.user_id') ?? Auth::id();
            $currentRoleId = session('user_data.role_id');

            $orgId = ($currentRoleId == 1)
                ? $request->organization_id
                : session('user_data.organization_id');

            $status = 'DRAFT';
            $approvedBy = null;
            $approvedAt = null;
            $signerUser = null;
            $shouldApprove = false;

            if (in_array($currentRoleId, [1, 2])) {
                $signerUser = $this->getSigner($currentRoleId, $orgId, $currentUserId);

                if (!$signerUser) {
                    throw new \Exception("Gagal: Tidak ditemukan User Manager di organisasi ini untuk tanda tangan.");
                }
                $shouldApprove = true;
            } else if ($currentRoleId == 3) {
                $signerUser = User::find($currentUserId);
                $shouldApprove = true;
            }

            if ($shouldApprove && $signerUser) {
                if (empty($signerUser->signature)) {
                    $roleName = ($currentRoleId == 3) ? "Anda" : "Manager";
                    throw new \Exception("Gagal Approve: {$roleName} belum mengatur tanda tangan digital di Profil.");
                }

                $status = 'APPROVED';
                $approvedBy = $signerUser->user_id;
                $approvedAt = Carbon::now();
            }

            $lampiranFinal = [];
            if ($request->has('lampiran')) {
                foreach ($request->lampiran as $item) {
                    $lampiranFinal[] = [
                        'judul_lampiran' => $item['judul'],
                        'foto_sebelum'   => $item['foto_sebelum']->store('laporan/sebelum', 'public'),
                        'foto_proses'    => $item['foto_proses']->store('laporan/proses', 'public'),
                        'foto_sesudah'   => $item['foto_sesudah']->store('laporan/sesudah', 'public'),
                    ];
                }
            }

            LaporanPekerjaan::create([
                'organization_id'     => $request->organization_id,
                'judul_laporan'       => $request->judul_laporan,
                'dasar_pelaksanaan'   => $request->dasar_pelaksanaan,
                'mulai_pelaksanaan'   => $request->mulai_pelaksanaan,
                'selesai_pelaksanaan' => $request->selesai_pelaksanaan,
                'lingkup_pekerjaan'   => $request->lingkup_pekerjaan,
                'hasil_pekerjaan'     => $request->hasil_pekerjaan,
                'lampiran'            => $lampiranFinal,
                'created_by'          => $currentUserId,
                'status'              => $status,
                'approved_by'         => $approvedBy,
                'approved_at'         => $approvedAt,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil disimpan dengan status: ' . $status,
                'redirect' => route(session('user_data.short_role_name') . '.laporan-pekerjaan.index')
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
        $laporan = LaporanPekerjaan::with(['organization', 'creator', 'approver'])->findOrFail($id);

        $option = Option::find(self::OPTION_ID);
        $allSettings = $option ? $option->text_value : [];
        $pdfSettings = $allSettings[$laporan->organization_id] ?? [];

        return view('laporan-pekerjaan.partials.detail', compact('laporan', 'pdfSettings'));
    }

    public function edit($id)
    {
        $laporan = LaporanPekerjaan::findOrFail($id);

        $currentUserId = session('user_data.user_id');
        $roleId = session('user_data.role_id');
        $isSuperUser = in_array($roleId, [1, 2, 3]);

        if ($laporan->created_by != $currentUserId && !$isSuperUser) {
            abort(403, 'Unauthorized action.');
        }

        $data['organizations'] = [];
        if ($roleId == 1) {
            $data['organizations'] = Organization::orderBy('organization_name', 'asc')->get();
        }

        $data['laporan'] = $laporan;

        return view('laporan-pekerjaan.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $laporan = LaporanPekerjaan::findOrFail($id);

        $rules = [
            'judul_laporan'       => 'required|string|max:255',
            'dasar_pelaksanaan'   => 'required|string',
            'mulai_pelaksanaan'   => 'required|date',
            'selesai_pelaksanaan' => 'required|date|after_or_equal:mulai_pelaksanaan',
            'lingkup_pekerjaan'   => 'required|string',
            'hasil_pekerjaan'     => 'required|string',
            'lampiran'            => 'required|array|min:1',
        ];

        if (session('user_data.role_id') == 1) {
            $rules['organization_id'] = 'required|exists:organizations,organization_id';
        }

        $messages = [
            'required'             => ':attribute wajib diisi.',
            'date'                 => ':attribute harus berupa tanggal valid.',
            'after_or_equal'       => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'lampiran.required'    => 'Minimal harus ada 1 lampiran.',
            'organization_id.required' => 'Organisasi wajib dipilih (Admin).',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        $validator->after(function ($validator) use ($request) {
            if ($request->lampiran) {
                foreach ($request->lampiran as $key => $item) {
                    if (empty($item['judul'])) {
                        $validator->errors()->add("lampiran.$key.judul", "Judul lampiran ke-" . ($key + 1) . " wajib diisi.");
                    }

                    if (empty($item['foto_sebelum']) && empty($item['existing_foto_sebelum'])) {
                        $validator->errors()->add("lampiran.$key.foto_sebelum", "Foto 'Sebelum' baris ke-" . ($key + 1) . " wajib diupload.");
                    }
                    if (empty($item['foto_proses']) && empty($item['existing_foto_proses'])) {
                        $validator->errors()->add("lampiran.$key.foto_proses", "Foto 'Proses' baris ke-" . ($key + 1) . " wajib diupload.");
                    }
                    if (empty($item['foto_sesudah']) && empty($item['existing_foto_sesudah'])) {
                        $validator->errors()->add("lampiran.$key.foto_sesudah", "Foto 'Sesudah' baris ke-" . ($key + 1) . " wajib diupload.");
                    }
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $lampiranFinal = [];

            if ($request->has('lampiran')) {
                foreach ($request->lampiran as $item) {
                    $pathSebelum = $item['existing_foto_sebelum'] ?? null;
                    if (isset($item['foto_sebelum']) && $item['foto_sebelum'] instanceof \Illuminate\Http\UploadedFile) {
                        if ($pathSebelum && Storage::disk('public')->exists($pathSebelum)) {
                            Storage::disk('public')->delete($pathSebelum);
                        }
                        $pathSebelum = $item['foto_sebelum']->store('laporan/sebelum', 'public');
                    }

                    $pathProses = $item['existing_foto_proses'] ?? null;
                    if (isset($item['foto_proses']) && $item['foto_proses'] instanceof \Illuminate\Http\UploadedFile) {
                        if ($pathProses && Storage::disk('public')->exists($pathProses)) {
                            Storage::disk('public')->delete($pathProses);
                        }
                        $pathProses = $item['foto_proses']->store('laporan/proses', 'public');
                    }

                    $pathSesudah = $item['existing_foto_sesudah'] ?? null;
                    if (isset($item['foto_sesudah']) && $item['foto_sesudah'] instanceof \Illuminate\Http\UploadedFile) {
                        if ($pathSesudah && Storage::disk('public')->exists($pathSesudah)) {
                            Storage::disk('public')->delete($pathSesudah);
                        }
                        $pathSesudah = $item['foto_sesudah']->store('laporan/sesudah', 'public');
                    }

                    $lampiranFinal[] = [
                        'judul_lampiran' => $item['judul'],
                        'foto_sebelum'   => $pathSebelum,
                        'foto_proses'    => $pathProses,
                        'foto_sesudah'   => $pathSesudah,
                    ];
                }
            }

            $updateData = [
                'judul_laporan'       => $request->judul_laporan,
                'dasar_pelaksanaan'   => $request->dasar_pelaksanaan,
                'mulai_pelaksanaan'   => $request->mulai_pelaksanaan,
                'selesai_pelaksanaan' => $request->selesai_pelaksanaan,
                'lingkup_pekerjaan'   => $request->lingkup_pekerjaan,
                'hasil_pekerjaan'     => $request->hasil_pekerjaan,
                'lampiran'            => $lampiranFinal,

                'status'              => 'DRAFT',
                'approved_by'         => null,
                'approved_at'         => null,
                'revision_note'       => null
            ];

            if (session('user_data.role_id') == 1) {
                $updateData['organization_id'] = $request->organization_id;
            }

            $laporan->update($updateData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil diperbarui. Status dikembalikan ke DRAFT.',
                'redirect' => route(session('user_data.short_role_name') . '.laporan-pekerjaan.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $laporan = LaporanPekerjaan::findOrFail($id);

            $userId = session('user_data.user_id');
            $roleId = session('user_data.role_id');

            if (!empty($laporan->lampiran) && is_array($laporan->lampiran)) {
                foreach ($laporan->lampiran as $item) {
                    $paths = [
                        $item['foto_sebelum'] ?? null,
                        $item['foto_proses']  ?? null,
                        $item['foto_sesudah'] ?? null,
                    ];

                    foreach ($paths as $path) {
                        if ($path && Storage::disk('public')->exists($path)) {
                            Storage::disk('public')->delete($path);
                        }
                    }
                }
            }

            $laporan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen Laporan Pekerjaan dan lampiran berhasil dihapus.'
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
        $laporan = LaporanPekerjaan::findOrFail($id);
        $currentUserId = session('user_data.user_id');
        $currentRoleId = session('user_data.role_id');
        $orgId = session('user_data.organization_id');

        if (!in_array($currentRoleId, [1, 2, 3])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: Anda tidak memiliki akses untuk menyetujui dokumen ini.'], 403);
        }

        $signerUser = $this->getSigner($currentRoleId, $orgId, $currentUserId);

        if (!$signerUser) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal: Tidak ditemukan User dengan Role Manager di organisasi ini untuk tanda tangan.'
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

        $laporan->update([
            'status' => 'APPROVED',
            'approved_by' => $signerUser->user_id,
            'approved_at' => Carbon::now()
        ]);

        return response()->json(['success' => true, 'message' => 'Laporan Pekerjaan Disetujui.']);
    }

    public function revision(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string'
        ]);

        $laporan = LaporanPekerjaan::findOrFail($id);

        $laporan->update([
            'status' => 'REVISION',
            'revision_note' => $request->reason,
        ]);

        return response()->json(['success' => true, 'message' => 'Dokumen dikembalikan untuk revisi.']);
    }

    public function exportPdf($id)
    {
        $laporan = LaporanPekerjaan::with(['organization', 'creator'])->findOrFail($id);
        if (Auth::user()->role_id == 1) {
            $orgId = $laporan->organization_id;
        } else {
            $orgId = session('user_data.organization_id');
        }

        $option = Option::find(self::OPTION_ID);
        $allSettings = $option ? $option->text_value : [];
        $pdfSettings = $allSettings[$orgId] ?? [];

        if (empty($pdfSettings)) {
            return redirect()->back()->with('error', 'Gagal export: Pengaturan PDF Laporan Pekerjaan belum diatur untuk organisasi ini. Silahkan atur dahulu.');
        }

        $pdf = Pdf::loadView('laporan-pekerjaan.pdf', compact('laporan', 'pdfSettings'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan_Pekerjaan_' . $laporan->laporan_pekerjaan_id . '.pdf');
    }
}
