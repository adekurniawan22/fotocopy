<?php

namespace App\Http\Controllers;

use App\Exports\ToolExport;
use App\Models\HistoryTool;
use App\Models\Organization;
use App\Models\Spki;
use App\Models\Tool;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Exception;


class ToolController extends Controller
{
    private const ADMIN_ROLE_ID = 1;

    private function isAdmin($user): bool
    {
        return $user->role_id == self::ADMIN_ROLE_ID;
    }

   public function index(Request $request)
    {
        $user = Auth::user();
        $keyword = $request->input('keyword');
        $limit = $request->input('limit', 10);
        $warehouseId = $request->input('warehouse_id');

        $query = Tool::with(['organization', 'warehouse'])
            ->whereHas('warehouse', function ($q) {
                $q->where('is_active', 1);
            });

        if (!$this->isAdmin($user)) {
            $query->where('organization_id', $user->organization_id);
        }

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', '%' . $keyword . '%')
                    ->orWhere('jenis', 'like', '%' . $keyword . '%')
                    ->orWhere('merk', 'like', '%' . $keyword . '%');
            });
        }

        $query->orderBy('nama', 'asc');
        $tools = $query->paginate($limit);

        $historyQuery = HistoryTool::where('is_returned', 0)->where('status', 'approved');

        if (!$this->isAdmin($user)) {
            $historyQuery->where('organization_id', $user->organization_id);
        }

        $activeHistories = $historyQuery->get();
        $borrowedCounts = [];

        foreach ($activeHistories as $history) {
            $listTools = is_array($history->list_tools) ? $history->list_tools : json_decode($history->list_tools, true);

            if (!empty($listTools['tools']) && is_array($listTools['tools'])) {
                foreach ($listTools['tools'] as $item) {
                    $tId = $item['id'] ?? $item['tool_id'] ?? null;
                    $qty = $item['qty'] ?? $item['jumlah'] ?? 0;

                    if ($tId) {
                        if (!isset($borrowedCounts[$tId])) {
                            $borrowedCounts[$tId] = 0;
                        }
                        $borrowedCounts[$tId] += (int) $qty;
                    }
                }
            }
        }


        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');

        $spkiQuery = Spki::where('status', 'APPROVED')
            ->whereDate('mulai_pelaksanaan', '<=', $today)
            ->whereDate('selesai_pelaksanaan', '>=', $today);

        if (!$this->isAdmin($user)) {
            $spkiQuery->where('organization_id', $user->organization_id);
        }

        $activeSpkis = $spkiQuery->get(['peralatan']);

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

        $tools->getCollection()->transform(function ($tool) use ($borrowedCounts) {
            $dipinjam = $borrowedCounts[$tool->tool_id] ?? 0;
            
            $tool->stok_dipinjam = $dipinjam;
            $tool->stok_real = max(0, $tool->jumlah - $dipinjam);
            
            return $tool;
        });

        if ($request->ajax()) {
            return view('tool.partials.table_data', [
                'tools' => $tools
            ]);
        }

        $organizations = Organization::orderBy('organization_name')->get();
        $warehouseQuery = Warehouse::where('is_active', 1)->orderBy('warehouse_name');
        if (!$this->isAdmin($user)) {
            $warehouseQuery->where('organization_id', $user->organization_id);
        }
        $warehouses = $warehouseQuery->get();

        return view('tool.list', [
            'tools' => $tools,
            'organizations' => $organizations,
            'warehouses' => $warehouses,
            'keyword' => $keyword,
            'limit' => $limit,
        ]);
    }

    public function show(Tool $tool): JsonResponse
    {
        $user = Auth::user();

        if (!$this->isAdmin($user) && $tool->organization_id !== $user->organization_id) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $tool->load(['warehouse', 'organization']);

        $historyQuery = HistoryTool::where('is_returned', 0)->where('status', 'approved');

        if (!$this->isAdmin($user)) {
            $historyQuery->where('organization_id', $user->organization_id);
        }

        $activeHistories = $historyQuery->get();

        $totalBorrowed = 0;

        foreach ($activeHistories as $history) {
            if (!empty($history->list_tools['tools']) && is_array($history->list_tools['tools'])) {
                foreach ($history->list_tools['tools'] as $item) {
                    if ($item['id'] == $tool->tool_id) {
                        $totalBorrowed += (int) $item['qty'];
                    }
                }
            }
        }

        $tool->stok_dipinjam = $totalBorrowed;
        $tool->stok_real = max(0, $tool->jumlah - $totalBorrowed);

        return response()->json($tool);
    }

    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        $isAdmin = $this->isAdmin($user);

        $organizationId = $request->input('organization_id') ?? $user->organization_id;

        $warehouseId = $request->input('warehouse_id');

        $rules = [
            'warehouse_id' => 'required|exists:warehouses,warehouse_id',
            'jenis' => 'required|string|max:255',
            'nama' => [
                'required',
                'string',
                'max:255',
                'max:255',
                Rule::unique('tools')->where('warehouse_id', $warehouseId)
            ],
            'merk' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'jumlah' => 'required|integer|min:0',
            'satuan' => 'required|string|max:100',
            'tanggal_pengadaan' => 'nullable|date',
            'tanggal_kadaluarsa' => 'nullable|date|after_or_equal:tanggal_pengadaan',
        ];

        if ($isAdmin) {
            $rules['organization_id'] = 'required|exists:organizations,organization_id';
        }

        $messages = [
            'warehouse_id.required' => 'Gudang wajib dipilih.',
            'warehouse_id.exists' => 'Gudang yang dipilih tidak valid.',
            'organization_id.required' => 'Organisasi wajib dipilih.',
            'organization_id.exists' => 'Organisasi yang dipilih tidak valid.',
            'jenis.required' => 'Jenis alat wajib diisi.',
            'jenis.max' => 'Jenis alat maksimal 255 karakter.',
            'nama.required' => 'Nama alat wajib diisi.',
            'nama.max' => 'Nama alat maksimal 255 karakter.',
            'nama.unique' => 'Nama alat sudah ada di gudang ini.',
            'merk.max' => 'Merk maksimal 255 karakter.',
            'jumlah.required' => 'Jumlah alat wajib diisi.',
            'jumlah.integer' => 'Jumlah harus berupa angka bulat.',
            'jumlah.min' => 'Jumlah minimal 0.',
            'satuan.required' => 'Satuan alat wajib diisi.',
            'satuan.max' => 'Satuan maksimal 100 karakter.',
            'tanggal_pengadaan.date' => 'Format tanggal pengadaan tidak valid.',
            'tanggal_kadaluarsa.date' => 'Format tanggal kadaluarsa tidak valid.',
            'tanggal_kadaluarsa.after_or_equal' => 'Tanggal kadaluarsa tidak boleh sebelum tanggal pengadaan.',
        ];

        $validator = Validator::make(array_merge($request->all(), [
            'organization_id' => $organizationId
        ]), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!$isAdmin) {
            $warehouse = Warehouse::find($warehouseId);
            if ($warehouse->organization_id !== $user->organization_id) {
                return response()->json(['errors' => ['warehouse_id' => ['Anda tidak punya akses ke gudang ini.']]], 403);
            }
        }

        Tool::create([
            'organization_id' => $organizationId,
            'warehouse_id' => $warehouseId,
            'jenis' => $request->jenis,
            'nama' => $request->nama,
            'merk' => $request->merk,
            'deskripsi' => $request->deskripsi,
            'jumlah' => $request->jumlah,
            'satuan' => $request->satuan,
            'tanggal_pengadaan' => $request->tanggal_pengadaan,
            'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
        ]);

        return response()->json(['success' => 'Alat kerja berhasil ditambahkan.']);
    }

    public function update(Request $request, Tool $tool): JsonResponse
    {
        $user = Auth::user();
        $isAdmin = $this->isAdmin($user);

        if (!$isAdmin && $tool->organization_id !== $user->organization_id) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $targetOrganizationId = $isAdmin ? $request->input('organization_id') : $tool->organization_id;
        $targetWarehouseId = $request->input('warehouse_id');

        $rules = [
            'warehouse_id' => 'required|exists:warehouses,warehouse_id',
            'jenis' => 'required|string|max:255',
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tools')
                    ->where('warehouse_id', $targetWarehouseId)
                    ->ignore($tool->tool_id, 'tool_id')
            ],
            'merk' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'jumlah' => 'required|integer|min:0',
            'satuan' => 'required|string|max:100',
            'tanggal_pengadaan' => 'nullable|date',
            'tanggal_kadaluarsa' => 'nullable|date|after_or_equal:tanggal_pengadaan',
        ];

        if ($isAdmin) {
            $rules['organization_id'] = 'required|exists:organizations,organization_id';
        }

        $messages = [
            'warehouse_id.required' => 'Gudang wajib dipilih.',
            'warehouse_id.exists' => 'Gudang yang dipilih tidak valid.',
            'organization_id.required' => 'Organisasi wajib dipilih.',
            'organization_id.exists' => 'Organisasi yang dipilih tidak valid.',
            'jenis.required' => 'Jenis alat wajib diisi.',
            'jenis.max' => 'Jenis alat maksimal 255 karakter.',
            'nama.required' => 'Nama alat wajib diisi.',
            'nama.max' => 'Nama alat maksimal 255 karakter.',
            'nama.unique' => 'Nama alat sudah ada di gudang ini.',
            'merk.max' => 'Merk maksimal 255 karakter.',
            'jumlah.required' => 'Jumlah alat wajib diisi.',
            'jumlah.integer' => 'Jumlah harus berupa angka bulat.',
            'jumlah.min' => 'Jumlah minimal 0.',
            'satuan.required' => 'Satuan alat wajib diisi.',
            'satuan.max' => 'Satuan maksimal 100 karakter.',
            'tanggal_pengadaan.date' => 'Format tanggal pengadaan tidak valid.',
            'tanggal_kadaluarsa.date' => 'Format tanggal kadaluarsa tidak valid.',
            'tanggal_kadaluarsa.after_or_equal' => 'Tanggal kadaluarsa tidak boleh sebelum tanggal pengadaan.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!$isAdmin) {
            $warehouse = Warehouse::find($targetWarehouseId);
            if ($warehouse->organization_id !== $user->organization_id) {
                return response()->json(['errors' => ['warehouse_id' => ['Anda tidak punya akses ke gudang ini.']]], 403);
            }
        }

        $dataToUpdate = [
            'warehouse_id' => $targetWarehouseId,
            'jenis' => $request->jenis,
            'nama' => $request->nama,
            'merk' => $request->merk,
            'deskripsi' => $request->deskripsi,
            'jumlah' => $request->jumlah,
            'satuan' => $request->satuan,
            'tanggal_pengadaan' => $request->tanggal_pengadaan,
            'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
        ];

        if ($isAdmin) {
            $dataToUpdate['organization_id'] = $targetOrganizationId;
        }

        $tool->update($dataToUpdate);

        return response()->json(['success' => 'Alat kerja berhasil diperbarui.']);
    }

    public function destroy(Tool $tool): JsonResponse
    {
        $user = Auth::user();

        if (!$this->isAdmin($user) && $tool->organization_id !== $user->organization_id) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        try {
            $tool->delete();
            return response()->json(['success' => 'Alat kerja berhasil dihapus.']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Gagal menghapus data. Data mungkin terkait dengan data lain.'], 500);
        }
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $keyword = $request->input('keyword');
        $warehouseId = $request->input('warehouse_id');

        $waktuIndonesia = now()->setTimezone('Asia/Jakarta')->format('Y-m-d H.i');

        $fileName = "Data_Alat_Kerja_{$waktuIndonesia}.xlsx";

        return Excel::download(new ToolExport($user, $keyword, $warehouseId), $fileName);
    }
}
