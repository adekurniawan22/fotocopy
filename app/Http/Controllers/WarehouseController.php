<?php

namespace App\Http\Controllers;

use App\Exports\WarehouseExport;
use App\Models\HistoryTool;
use App\Models\Organization;
use App\Models\Spki;
use App\Models\Tool;
use App\Models\Warehouse;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class WarehouseController extends Controller
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

        $query = Warehouse::with('organization')->withCount('tool');

        if (!$this->isAdmin($user)) {
            $query->where('organization_id', $user->organization_id);
        }

        if ($keyword) {
            $query->where('warehouse_name', 'like', '%' . $keyword . '%');
        }

        $query->orderBy('warehouse_name', 'asc');
        $warehouses = $query->paginate($limit);
        $warehouses->appends($request->only(['keyword', 'limit']));

        if ($request->ajax()) {
            return view('warehouse.partials.table_data', [
                'warehouses' => $warehouses
            ]);
        }

        $organizations = Organization::orderBy('organization_name')->get();

        return view('warehouse.list', [
            'warehouses' => $warehouses,
            'organizations' => $organizations,
            'keyword' => $keyword,
            'limit' => $limit,
        ]);
    }

    public function show(Warehouse $warehouse): JsonResponse
    {
        $user = Auth::user();

        if (!$this->isAdmin($user) && $warehouse->organization_id !== $user->organization_id) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        return response()->json($warehouse);
    }

    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        $organizationId = $request->input('organization_id') ?? $user->organization_id;

        if ($this->isAdmin($user) && !$request->input('organization_id')) {
            return response()->json(['errors' => ['organization_id' => ['Admin wajib memilih organisasi.']]], 422);
        }

        if (!$organizationId) {
            return response()->json(['errors' => ['organization_id' => ['Organisasi tidak dapat ditentukan.']]], 422);
        }

        $messages = [
            'warehouse_name.required' => 'Nama gudang wajib diisi.',
            'warehouse_name.string' => 'Nama gudang harus berupa teks.',
            'warehouse_name.max' => 'Nama gudang maksimal 255 karakter.',
            'warehouse_name.unique' => 'Nama gudang sudah ada di organisasi ini.',
            'organization_id.exists' => 'Organisasi yang dipilih tidak valid.',
            'is_active.required' => 'Status aktif wajib dipilih.',
            'is_active.in' => 'Status aktif harus berupa Ya atau Tidak.',
        ];

        $validator = Validator::make($request->all(), [
            'warehouse_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('warehouses')->where('organization_id', $organizationId)
            ],
            'organization_id' => 'nullable|exists:organizations,organization_id',
            'is_active' => 'required|in:0,1',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Warehouse::create([
            'warehouse_name' => $request->warehouse_name,
            'organization_id' => $organizationId,
            'is_active' => $request->boolean('is_active'),
        ]);

        return response()->json(['success' => 'Data gudang berhasil ditambahkan.']);
    }

    public function update(Request $request, Warehouse $warehouse): JsonResponse
    {
        $user = Auth::user();

        if (!$this->isAdmin($user) && $warehouse->organization_id !== $user->organization_id) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $targetOrganizationId = $warehouse->organization_id;
        if ($this->isAdmin($user) && $request->has('organization_id')) {
            $targetOrganizationId = $request->input('organization_id');
        }

        $messages = [
            'warehouse_name.required' => 'Nama gudang wajib diisi.',
            'warehouse_name.string' => 'Nama gudang harus berupa teks.',
            'warehouse_name.max' => 'Nama gudang maksimal 255 karakter.',
            'warehouse_name.unique' => 'Nama gudang sudah ada di organisasi ini.',
            'organization_id.required' => 'Organisasi wajib dipilih.',
            'organization_id.exists' => 'Organisasi yang dipilih tidak valid.',
            'is_active.required' => 'Status aktif wajib dipilih.',
            'is_active.in' => 'Status aktif harus berupa Ya atau Tidak.',
        ];

        $rules = [
            'warehouse_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('warehouses')
                    ->where('organization_id', $targetOrganizationId)
                    ->ignore($warehouse->warehouse_id, 'warehouse_id')
            ],
            'is_active' => 'required|in:0,1',
        ];

        if ($this->isAdmin($user) && $request->has('organization_id')) {
            $rules['organization_id'] = 'required|exists:organizations,organization_id';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $dataToUpdate = [
            'warehouse_name' => $request->warehouse_name,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($this->isAdmin($user) && $request->has('organization_id')) {
            $dataToUpdate['organization_id'] = $request->input('organization_id');
        }

        $warehouse->update($dataToUpdate);

        return response()->json(['success' => 'Data gudang berhasil diperbarui.']);
    }

    public function destroy(Warehouse $warehouse): JsonResponse
    {
        $user = Auth::user();

        if (!$this->isAdmin($user) && $warehouse->organization_id !== $user->organization_id) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        try {
            $warehouse->delete();
            return response()->json(['success' => 'Data gudang berhasil dihapus.']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Gagal menghapus data. Data mungkin terkait dengan data lain.'], 500);
        }
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $keyword = $request->input('keyword');

        $waktuIndonesia = now()->setTimezone('Asia/Jakarta')->format('Y-m-d H.i');
        $fileName = "Data_Gudang_{$waktuIndonesia}.xlsx";

        return Excel::download(new WarehouseExport($user, $keyword), $fileName);
    }

    public function updateStatus(Request $request, Warehouse $warehouse): JsonResponse
    {
        $user = Auth::user();

        if (!$this->isAdmin($user) && $warehouse->organization_id !== $user->organization_id) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak.'
            ], 403);
        }

        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        try {
            $warehouse->update([
                'is_active' => $request->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status gudang berhasil diperbarui.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status gudang.'
            ], 500);
        }
    }

    public function getWarehousesByOrganization(Request $request, Organization $organization): JsonResponse
    {
        $user = Auth::user();

        if (!$this->isAdmin($user) && $organization->organization_id !== $user->organization_id) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $warehouses = Warehouse::where('organization_id', $organization->organization_id)
            ->where('is_active', true)
            ->orderBy('warehouse_name')
            ->get(['warehouse_id', 'warehouse_name']);

        return response()->json($warehouses);
    }

    public function tools(Request $request, Warehouse $warehouse)
    {
        $user = Auth::user();

        if (!$this->isAdmin($user) && $warehouse->organization_id !== $user->organization_id) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $limit = $request->input('limit', 5);

        $tools = Tool::where('warehouse_id', $warehouse->warehouse_id)
            ->orderBy('nama', 'asc')
            ->paginate($limit);

        $activeHistories = HistoryTool::where('organization_id', $warehouse->organization_id)
            ->where('is_returned', 0)
            ->where('status', 'approved')
            ->get();

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

        $activeSpkis = Spki::where('organization_id', $warehouse->organization_id)
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

        $tools->getCollection()->transform(function ($tool) use ($borrowedCounts) {
            $dipinjam = $borrowedCounts[$tool->tool_id] ?? 0;

            $tool->stok_dipinjam = $dipinjam;
            $tool->stok_real = max(0, $tool->jumlah - $dipinjam);

            return $tool;
        });

        if ($request->ajax()) {
            return view('warehouse.partials.tools_table', compact('tools', 'warehouse'))->render();
        }

        return view('warehouse.partials.tools_table', compact('tools', 'warehouse'));
    }
}
