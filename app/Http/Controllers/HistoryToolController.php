<?php

namespace App\Http\Controllers;

use App\Models\HistoryTool;
use App\Models\MethodTool;
use App\Models\Organization;
use App\Models\Tool;
use App\Models\Option;
use App\Models\Role;
use App\Models\Spki;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class HistoryToolController extends Controller
{
    private const OPTION_ID = 1;

    public function index(Request $request)
    {
        $query = HistoryTool::query()->with(['organization', 'creator']);

        if (Auth::user()->role_id != 1) {
            $query->where('organization_id', session('user_data.organization_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_returned', $request->status);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('exit_date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('keterangan', 'like', "%{$keyword}%")
                    ->orWhereHas('creator', function ($u) use ($keyword) {
                        $u->where('name', 'like', "%{$keyword}%");
                    })
                    ->orWhere('list_user->pj->name', 'like', "%{$keyword}%")
                    ->orWhere('list_user->receiver->name', 'like', "%{$keyword}%")
                    ->orWhere('list_user->giver->name', 'like', "%{$keyword}%");
            });
        }

        $histories = $query->latest()->paginate(10);

        if ($request->ajax()) {
            return view('history-tool.partials.table_data', compact('histories'))->render();
        }

        return view('history-tool.list', compact('histories'));
    }

    public function create()
    {
        $data['organizations'] = (Auth::user()->role_id == 1) ? Organization::all() : [];
        $data['users'] = User::where('organization_id', session('user_data.organization_id') ?? 0)->get();
        return view('history-tool.add', $data);
    }

    public function store(Request $request)
    {
        $rules = [
            'type' => 'required|in:Internal,Eksternal',
            'exit_date' => 'required|date',
            'tool_source' => 'required|in:method,manual',
            'organization_id' => Auth::user()->role_id == 1 ? 'required|exists:organizations,organization_id' : 'nullable',

            'list_tools' => 'required|array|min:1',
            'list_tools.*.id' => 'required|integer|exists:tools,tool_id',
            'list_tools.*.qty' => 'required|integer|min:1',
        ];

        if ($request->type == 'Internal') {
            $rules = array_merge($rules, $this->resolveUserValidation($request, 'internal_pj', 'Penanggung Jawab'));
        } else {
            $rules = array_merge($rules, $this->resolveUserValidation($request, 'external_receiver', 'Penerima'));
            $rules = array_merge($rules, $this->resolveUserValidation($request, 'external_giver', 'Pemberi'));
        }

        $messages = [
            'required' => ':attribute wajib diisi.',
            'in'       => 'Pilihan :attribute tidak valid.',
            'date'     => ':attribute harus berupa tanggal yang valid.',
            'exists'   => ':attribute tidak ditemukan di dalam sistem.',
            'array'    => ':attribute harus berupa daftar data.',
            'min'      => ':attribute minimal bernilai :min.',
            'max'      => ':attribute maksimal bernilai :max.',
            'integer'  => ':attribute harus berupa angka.',
            'image'    => ':attribute harus berupa file gambar.',
            'string'   => ':attribute harus berupa teks.',
            'list_tools.required' => 'Anda wajib memilih minimal satu alat.',
            'list_tools.min'      => 'Anda wajib memilih minimal satu alat.',
        ];

        $attributes = [
            'type' => 'Tipe Peminjaman',
            'exit_date' => 'Tanggal Keluar',
            'organization_id' => 'Organisasi',
            'tool_source' => 'Sumber Alat',
            'list_tools' => 'Daftar Alat',
            'list_tools.*.qty' => 'Jumlah Alat',
            'list_tools.*.id' => 'Alat',

            'internal_pj_select' => 'Penanggung Jawab (User)',
            'internal_pj_text' => 'Nama Penanggung Jawab',
            'internal_pj_sign_file' => 'File Tanda Tangan PJ',
            'internal_pj_sign_canvas' => 'Tanda Tangan Digital PJ',

            'external_receiver_select' => 'Penerima (User)',
            'external_receiver_text' => 'Nama Penerima',
            'external_receiver_sign_file' => 'File Tanda Tangan Penerima',
            'external_receiver_sign_canvas' => 'Tanda Tangan Digital Penerima',

            'external_giver_select' => 'Pemberi (User)',
            'external_giver_text' => 'Nama Pemberi',
            'external_giver_sign_file' => 'File Tanda Tangan Pemberi',
            'external_giver_sign_canvas' => 'Tanda Tangan Digital Pemberi',
        ];

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        $validator->after(function ($validator) use ($request) {
            $listTools = $request->list_tools ?? [];
            foreach ($listTools as $index => $toolItem) {
                if (isset($toolItem['id']) && isset($toolItem['qty'])) {
                    $tool = Tool::find($toolItem['id']);
                    if ($tool && $toolItem['qty'] > $tool->jumlah) {
                        $validator->errors()->add(
                            "list_tools.$index.qty",
                            "Stok {$tool->nama} tidak cukup. Tersedia: {$tool->jumlah}."
                        );
                    }
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $orgId = (Auth::user()->role_id == 1) ? $request->organization_id : session('user_data.organization_id');
        $listToolsData = ['method_name' => null, 'tools' => []];
        $submittedTools = $request->list_tools ?? [];

        if ($request->tool_source == 'method') {
            $method = MethodTool::find($request->method_id);
            if ($method) $listToolsData['method_name'] = $method->nama_method;
        }

        $toolsCollection = collect($submittedTools);

        $aggregatedTools = $toolsCollection->groupBy('id')->map(function ($items) {
            return [
                'id' => $items->first()['id'],
                'qty' => $items->sum('qty'),
            ];
        })->values();

        $toolsForHistory = [];

        foreach ($aggregatedTools as $toolItem) {
            $tool = Tool::find($toolItem['id']);
            if ($tool) {
                $toolsForHistory[] = [
                    'id' => $tool->tool_id,
                    'nama' => $tool->nama,
                    'satuan' => $tool->satuan,
                    'qty' => (int) $toolItem['qty']
                ];
            }
        }

        $listToolsData['tools'] = $toolsForHistory;

        $listUserData = [];
        if ($request->type == 'Internal') {
            $pjName = $this->resolveUserName($request, 'internal_pj');
            $pjSign = $this->handleSignature($request, 'internal_pj_sign_type', 'internal_pj_sign_file', 'internal_pj_sign_canvas');
            $listUserData['pj'] = ['name' => $pjName, 'sign' => $pjSign];
        } else {
            $receiverName = $this->resolveUserName($request, 'external_receiver');
            $receiverSign = $this->handleSignature($request, 'external_receiver_sign_type', 'external_receiver_sign_file', 'external_receiver_sign_canvas');
            $giverName = $this->resolveUserName($request, 'external_giver');
            $giverSign = $this->handleSignature($request, 'external_giver_sign_type', 'external_giver_sign_file', 'external_giver_sign_canvas');
            $listUserData['receiver'] = ['name' => $receiverName, 'sign' => $receiverSign];
            $listUserData['giver'] = ['name' => $giverName, 'sign' => $giverSign];
        }

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('history_tools', 'public');
        }

        $initialStatus = (session('user_data.role_id') == 3) ? 'approved' : 'draft';

        HistoryTool::create([
            'organization_id' => $orgId,
            'type' => $request->type,
            'status' => $initialStatus,
            'created_by' => Auth::id(),
            'list_tools' => $listToolsData,
            'list_user' => $listUserData,
            'exit_date' => $request->exit_date,
            'keterangan' => $request->keterangan,
            'foto' => $fotoPath,
            'is_returned' => 0,
            'return_date' => null
        ]);

        $message = ($initialStatus == 'approved')
            ? 'Riwayat berhasil ditambahkan dan langsung Disetujui.'
            : 'Riwayat berhasil disimpan sebagai Draft.';

        return redirect()->route(session('user_data.short_role_name') . '.history-tool.index')
            ->with('success', $message);
    }

    public function show($id)
    {
        $history = HistoryTool::with(['organization', 'creator'])->findOrFail($id);
        if (Auth::user()->role_id == 1) {
            $orgId = $history->organization_id;
        } else {
            $orgId = session('user_data.organization_id');
        }

        $option = Option::find(self::OPTION_ID);
        $allSettings = $option ? $option->text_value : [];
        $pdfSettings = $allSettings[$orgId] ?? [];

        if ($history->status != 'draft') {
            $approver = User::where('organization_id', $history->organization_id)
                ->where('role_id', 3)
                ->first();

            $approver = User::with('role')
                ->where('organization_id', $history->organization_id)
                ->where('role_id', 3)
                ->first();

            if ($approver) {
                $pdfSettings['nama_atasan'] = $approver->name;

                $pdfSettings['tanda_tangan'] = $approver->signature ?? null;

                $pdfSettings['jabatan_atasan'] = $approver->role->role_name ?? 'Kepala Gudang';
            }
        }

        if (request()->ajax()) {
            return view('history-tool.partials.detail', compact('history', 'pdfSettings'))->render();
        }

        abort(404);
    }

    public function edit($id)
    {
        $history = HistoryTool::findOrFail($id);

        if (Auth::user()->role_id != 1 && $history->organization_id != session('user_data.organization_id')) {
            abort(403);
        }

        $organizations = (Auth::user()->role_id == 1) ? Organization::all() : [];
        $users = User::where('organization_id', $history->organization_id)
            ->where('role_id', '!=', 1)
            ->get();

        $methods = MethodTool::where('organization_id', $history->organization_id)->get();
        $tools = Tool::where('organization_id', $history->organization_id)->get();

        return view('history-tool.edit', compact('history', 'organizations', 'users', 'methods', 'tools'));
    }

    public function update(Request $request, $id)
    {
        $history = HistoryTool::findOrFail($id);

        if (Auth::user()->role_id != 1 && $history->organization_id != session('user_data.organization_id')) {
            abort(403);
        }

        $rules = [
            'exit_date' => 'required|date',
            'tool_source' => 'required|in:method,manual',
            'list_tools' => 'required|array|min:1',
            'list_tools.*.id' => 'required|integer|exists:tools,tool_id',
            'list_tools.*.qty' => 'required|integer|min:1',
        ];

        if ($history->type == 'Internal') {
            $rules = array_merge($rules, $this->resolveUserValidation($request, 'internal_pj', 'Penanggung Jawab', true));
        } else {
            $rules = array_merge($rules, $this->resolveUserValidation($request, 'external_receiver', 'Penerima', true));
            $rules = array_merge($rules, $this->resolveUserValidation($request, 'external_giver', 'Pemberi', true));
        }

        $messages = [
            'required' => ':attribute wajib diisi.',
            'in'       => 'Pilihan :attribute tidak valid.',
            'date'     => ':attribute harus berupa tanggal yang valid.',
            'exists'   => ':attribute tidak ditemukan di dalam sistem.',
            'array'    => ':attribute harus berupa daftar data.',
            'min'      => ':attribute minimal bernilai :min.',
            'max'      => ':attribute maksimal bernilai :max.',
            'integer'  => ':attribute harus berupa angka.',
            'image'    => ':attribute harus berupa file gambar.',
            'string'   => ':attribute harus berupa teks.',
            'list_tools.required' => 'Anda wajib memilih minimal satu alat.',
            'list_tools.min'      => 'Anda wajib memilih minimal satu alat.',
        ];

        $attributes = [
            'exit_date' => 'Tanggal Keluar',
            'list_tools' => 'Daftar Alat',
            'tool_source' => 'Sumber Alat',
            'list_tools.*.qty' => 'Jumlah Alat',
            'list_tools.*.id' => 'Alat',

            'internal_pj_select' => 'Penanggung Jawab (User)',
            'internal_pj_text' => 'Nama Penanggung Jawab',
            'internal_pj_sign_file' => 'File Tanda Tangan PJ',
            'internal_pj_sign_canvas' => 'Tanda Tangan Digital PJ',

            'external_receiver_select' => 'Penerima (User)',
            'external_receiver_text' => 'Nama Penerima',
            'external_receiver_sign_file' => 'File Tanda Tangan Penerima',
            'external_receiver_sign_canvas' => 'Tanda Tangan Digital Penerima',

            'external_giver_select' => 'Pemberi (User)',
            'external_giver_text' => 'Nama Pemberi',
            'external_giver_sign_file' => 'File Tanda Tangan Pemberi',
            'external_giver_sign_canvas' => 'Tanda Tangan Digital Pemberi',
        ];

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        $validator->after(function ($validator) use ($request, $history) {
            $listTools = $request->list_tools ?? [];
            $oldTools = collect($history->list_tools['tools'] ?? [])->pluck('qty', 'id')->toArray();

            foreach ($listTools as $index => $toolItem) {
                if (isset($toolItem['id']) && isset($toolItem['qty'])) {
                    $tool = Tool::find($toolItem['id']);
                    $oldQty = $oldTools[$toolItem['id']] ?? 0;

                    if ($tool) {
                        $availableStock = $tool->jumlah + $oldQty;

                        if ($toolItem['qty'] > $availableStock) {
                            $validator->errors()->add(
                                "list_tools.$index.qty",
                                "Stok {$tool->nama} tidak cukup. Maksimal: {$availableStock}."
                            );
                        }
                    }
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $listToolsData = ['method_name' => null, 'tools' => []];

        if ($request->tool_source == 'method' && $request->method_id) {
            $method = MethodTool::find($request->method_id);
            if ($method) $listToolsData['method_name'] = $method->nama_method;
        } else {
            $listToolsData['method_name'] = $history->list_tools['method_name'] ?? null;
        }

        $toolsCollection = collect($request->list_tools);
        $aggregatedTools = $toolsCollection->groupBy('id')->map(function ($items) {
            return ['id' => $items->first()['id'], 'qty' => $items->sum('qty')];
        })->values();

        foreach ($aggregatedTools as $toolItem) {
            $tool = Tool::find($toolItem['id']);
            if ($tool) {
                $listToolsData['tools'][] = [
                    'id' => $tool->tool_id,
                    'nama' => $tool->nama,
                    'satuan' => $tool->satuan,
                    'qty' => (int) $toolItem['qty']
                ];
            }
        }

        $listUserData = $history->list_user;

        if ($history->type == 'Internal') {
            $pjName = $this->resolveUserName($request, 'internal_pj');
            $newPjSign = $this->handleSignature($request, 'internal_pj_sign_type', 'internal_pj_sign_file', 'internal_pj_sign_canvas');

            if ($newPjSign) {
                $this->deleteFile($listUserData['pj']['sign'] ?? null);
                $pjSign = $newPjSign;
            } else {
                $pjSign = $listUserData['pj']['sign'] ?? null;
            }

            $listUserData['pj'] = ['name' => $pjName, 'sign' => $pjSign];
        } else {
            $receiverName = $this->resolveUserName($request, 'external_receiver');
            $newReceiverSign = $this->handleSignature($request, 'external_receiver_sign_type', 'external_receiver_sign_file', 'external_receiver_sign_canvas');

            if ($newReceiverSign) {
                $this->deleteFile($listUserData['receiver']['sign'] ?? null);
                $receiverSign = $newReceiverSign;
            } else {
                $receiverSign = $listUserData['receiver']['sign'] ?? null;
            }

            $giverName = $this->resolveUserName($request, 'external_giver');
            $newGiverSign = $this->handleSignature($request, 'external_giver_sign_type', 'external_giver_sign_file', 'external_giver_sign_canvas');

            if ($newGiverSign) {
                $this->deleteFile($listUserData['giver']['sign'] ?? null);
                $giverSign = $newGiverSign;
            } else {
                $giverSign = $listUserData['giver']['sign'] ?? null;
            }

            $listUserData['receiver'] = ['name' => $receiverName, 'sign' => $receiverSign];
            $listUserData['giver'] = ['name' => $giverName, 'sign' => $giverSign];
        }

        $fotoPath = $history->foto;
        if ($request->hasFile('foto')) {
            $this->deleteFile($history->foto);
            $fotoPath = $request->file('foto')->store('history_tools', 'public');
        }

        $history->update([
            'list_tools' => $listToolsData,
            'list_user' => $listUserData,
            'exit_date' => $request->exit_date,
            'keterangan' => $request->keterangan,
            'foto' => $fotoPath,
        ]);

        return redirect()->route(session('user_data.short_role_name') . '.history-tool.index')->with('success', 'Riwayat berhasil diperbarui');
    }

    public function destroy($id)
    {
        $history = HistoryTool::findOrFail($id);

        if (Auth::user()->role_id != 1 && $history->organization_id != session('user_data.organization_id')) {
            abort(403);
        }

        $this->deleteFile($history->foto);

        $listUserData = $history->list_user;

        if ($history->type == 'Internal') {
            $this->deleteFile($listUserData['pj']['sign'] ?? null);
        } else {
            $this->deleteFile($listUserData['receiver']['sign'] ?? null);
            $this->deleteFile($listUserData['giver']['sign'] ?? null);
        }

        $history->delete();

        return redirect()->route(session('user_data.short_role_name') . '.history-tool.index')
            ->with('success', 'Riwayat berhasil dihapus dan file terkait telah dibersihkan.');
    }

    private function deleteFile($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function approve($id)
    {
        $history = HistoryTool::findOrFail($id);

        if ($history->status != 'draft') {
            return response()->json(['error' => 'Data sudah disetujui sebelumnya.'], 400);
        }

        $currentUserId = session('user_data.user_id');
        $currentRoleId = session('user_data.role_id');

        $orgId = $history->organization_id;

        $signerUser = $this->getSigner($currentRoleId, $orgId, $currentUserId);

        if (!$signerUser) {
            return response()->json([
                'error' => 'Gagal: Tidak ditemukan User dengan Role Assitant Manager di organisasi ini untuk tanda tangan.'
            ], 422);
        }

        if (empty($signerUser->signature)) {
            $roleName = "Anda";

            if (in_array($currentRoleId, [1, 2])) {
                $role3 = Role::where('role_id', 3)->first();
                $roleName = $role3 ? $role3->role_name : 'Manager / Kepala Gudang';
            }

            return response()->json([
                'error' => "Gagal Approve: {$roleName} belum mengatur tanda tangan di Profil. Silakan update profil terlebih dahulu."
            ], 422);
        }

        $history->update([
            'status' => 'approved',
            'is_returned' => 0,
        ]);

        return response()->json(['success' => 'Peminjaman berhasil disetujui.']);
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

    public function markAsReturned(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'return_date' => 'required|date',
        ], [
            'return_date.required' => 'Tanggal kembali wajib diisi.',
            'return_date.date' => 'Format tanggal kembali tidak valid.'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $history = HistoryTool::findOrFail($id);

        $history->update([
            'is_returned' => 1,
            'return_date' => $request->return_date
        ]);

        return response()->json(['success' => 'Status berhasil diperbarui menjadi Dikembalikan']);
    }

    public function getResources(Request $request)
    {
        $orgId = $request->organization_id;
        $historyId = $request->history_id;

        $methods = MethodTool::where('organization_id', $orgId)->get(['method_id', 'nama_method']);
        $users = User::with('role')
            ->where('organization_id', $orgId)
            ->whereNotIn('role_id', [1, 2])
            ->get(['user_id', 'name', 'role_id']);

        $tools = Tool::where('organization_id', $orgId)->get(['tool_id', 'nama', 'merk', 'jumlah']);

        $activeHistories = HistoryTool::where('organization_id', $orgId)
            ->where('is_returned', 0)
            ->where('status', 'approved')
            ->get(['list_tools']);

        $borrowedCounts = [];

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
                        if (!isset($borrowedCounts[$tId])) {
                            $borrowedCounts[$tId] = 0;
                        }
                        $borrowedCounts[$tId] += (int) $qty;
                    }
                }
            }
        }

        $currentTransactionCounts = [];
        if ($historyId) {
            $currentHistory = HistoryTool::find($historyId);
            $currListTools = is_array($currentHistory->list_tools) ? $currentHistory->list_tools : json_decode($currentHistory->list_tools, true);

            if ($currentHistory && !empty($currListTools['tools'])) {
                foreach ($currListTools['tools'] as $item) {
                    $tId = $item['id'] ?? $item['tool_id'] ?? null;
                    $qty = $item['qty'] ?? $item['jumlah'] ?? 0;
                    if ($tId) $currentTransactionCounts[$tId] = (int) $qty;
                }
            }
        }

        $tools->transform(function ($tool) use ($borrowedCounts, $currentTransactionCounts) {
            $totalDipinjam = $borrowedCounts[$tool->tool_id] ?? 0;
            $sisaGudang = max(0, $tool->jumlah - $totalDipinjam);
            $sedangDipakaiIni = $currentTransactionCounts[$tool->tool_id] ?? 0;

            $tool->jumlah = $sisaGudang + $sedangDipakaiIni;
            return $tool;
        });

        $methodTools = null;
        if ($request->method_id) {
            $method = MethodTool::find($request->method_id);
            if ($method) {
                $methodToolsRaw = is_array($method->list_tools) ? $method->list_tools : json_decode($method->list_tools, true);

                $methodTools = collect($methodToolsRaw)->map(function ($mTool) use ($tools) {
                    $masterTool = $tools->firstWhere('tool_id', $mTool['id'] ?? $mTool['tool_id']);
                    $mTool['current_stok'] = $masterTool ? $masterTool->jumlah : 0;
                    return $mTool;
                });
            }
        }

        return response()->json([
            'methods' => $methods,
            'tools' => $tools,
            'users' => $users,
            'method_tools' => $methodTools
        ]);
    }

    public function exportPdf($id)
    {
        $history = HistoryTool::with(['organization', 'creator'])->findOrFail($id);

        if (Auth::user()->role_id == 1) {
            $orgId = $history->organization_id;
        } else {
            $orgId = session('user_data.organization_id');
        }

        $option = Option::find(self::OPTION_ID);
        $allSettings = $option ? $option->text_value : [];
        $pdfSettings = $allSettings[$orgId] ?? [];

        if (empty($pdfSettings)) {
            return redirect()->back()->with('error', 'Gagal export: Pengaturan PDF Riwayat Gudang belum diatur untuk organisasi ini. Silahkan atur dahulu.');
        }

        if ($history->status != 'draft') {
            $approver = User::with('role')
                ->where('organization_id', $history->organization_id)
                ->where('role_id', 3)
                ->first();

            if ($approver) {
                $pdfSettings['nama_atasan'] = $approver->name;
                $pdfSettings['tanda_tangan'] = $approver->signature ?? null;

                $pdfSettings['jabatan_atasan'] = $approver->role->role_name ?? 'Kepala Gudang';
            }
        }

        $pdf = Pdf::loadView('history-tool.pdf', compact('history', 'pdfSettings'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Riwayat_Gudang_' . $history->history_tool_id . '.pdf');
    }

    private function resolveUserValidation(Request $request, $prefix, $label, $isUpdate = false)
    {
        $source = $request->input("{$prefix}_source");
        $sign_type = $request->input("{$prefix}_sign_type");

        $rules = [];

        if ($source == 'select') {
            $rules["{$prefix}_select"] = ['required', 'exists:users,user_id'];
        } else {
            $rules["{$prefix}_text"] = ['required', 'string', 'max:255'];
        }

        $signRule = $isUpdate ? 'nullable' : 'required';

        if ($sign_type == 'file') {
            $rules["{$prefix}_sign_file"] = [$signRule, 'image', 'max:15360'];
        } else {
            $rules["{$prefix}_sign_canvas"] = [$signRule, 'string'];
        }

        return $rules;
    }

    private function resolveUserName(Request $request, $prefix)
    {
        $source = $request->input("{$prefix}_source");
        if ($source == 'select') {
            $user = User::find($request->input("{$prefix}_select"));
            return $user ? $user->name : 'Unknown User';
        }
        return $request->input("{$prefix}_text");
    }

    private function handleSignature(Request $request, $typeKey, $fileKey, $canvasKey)
    {
        $type = $request->input($typeKey);

        if ($type == 'file' && $request->hasFile($fileKey)) {
            return $request->file($fileKey)->store('signatures', 'public');
        }

        if ($type == 'canvas' && $request->input($canvasKey)) {
            $base64 = $request->input($canvasKey);
            if (empty($base64)) return null;

            if (preg_match('/^data:image\/(\w+);base64,/', $base64)) {
                $image = substr($base64, strpos($base64, ',') + 1);
                $image = str_replace(' ', '+', $image);
                $imageName = 'signatures/' . Str::random(10) . '.png';
                Storage::disk('public')->put($imageName, base64_decode($image));
                return $imageName;
            }
        }

        return null;
    }
}
