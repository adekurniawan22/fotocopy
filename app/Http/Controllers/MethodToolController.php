<?php

namespace App\Http\Controllers;

use App\Models\MethodTool;
use App\Models\Tool;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MethodToolController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = MethodTool::query()->with('organization');

        if ($user->role_id != 1) {
            $query->where('organization_id', session('user_data.organization_id'));
        }

        if ($request->has('keyword') && $request->keyword != '') {
            $query->where('nama_method', 'like', '%' . $request->keyword . '%');
        }

        $methods = $query->paginate(10);

        $toolQuery = Tool::query();
        if ($user->role_id != 1) {
            $toolQuery->where('organization_id', session('user_data.organization_id'));
        }

        $available_tools = $toolQuery->get(['tool_id', 'nama', 'merk', 'jumlah']);

        $organizations = ($user->role_id == 1) ? Organization::all() : [];

        if ($request->ajax()) {
            return view('method.partials.table_data', compact('methods'))->render();
        }

        return view('method.list', compact('methods', 'available_tools', 'organizations'));
    }

    public function store(Request $request)
    {
        $validator = $this->validateRequest($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $orgId = Auth::user()->role_id == 1
                ? $request->organization_id
                : session('user_data.organization_id');

            MethodTool::create([
                'organization_id' => $orgId,
                'nama_method' => $request->nama_method,
                'list_tools' => $request->list_tools,
            ]);

            return response()->json(['success' => 'Metode berhasil ditambahkan']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }

    public function getToolsByOrganization(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,organization_id'
        ], [
            'organization_id.required' => 'Organisasi wajib dipilih.',
            'organization_id.exists' => 'Data organisasi tidak ditemukan.'
        ]);

        $tools = Tool::where('organization_id', $request->organization_id)
            ->select('tool_id', 'nama', 'merk', 'jumlah')
            ->get();

        return response()->json($tools);
    }

    public function show($id)
    {
        $method = MethodTool::findOrFail($id);

        $available_tools = Tool::where('organization_id', $method->organization_id)
            ->select('tool_id', 'nama', 'merk', 'jumlah')
            ->get();

        return response()->json([
            'method' => $method,
            'list_tools' => $method->list_tools,
            'available_tools' => $available_tools
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = $this->validateRequest($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $method = MethodTool::findOrFail($id);

            $data = [
                'nama_method' => $request->nama_method,
                'list_tools' => $request->list_tools,
            ];

            if (Auth::user()->role_id == 1) {
                $data['organization_id'] = $request->organization_id;
            }

            $method->update($data);

            return response()->json(['success' => 'Metode berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memperbarui data'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            MethodTool::destroy($id);
            return response()->json(['success' => 'Metode berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus data'], 500);
        }
    }

    private function validateRequest(Request $request)
    {
        $messages = [
            'required' => 'Kolom :attribute wajib diisi.',
            'string'   => 'Kolom :attribute harus berupa teks.',
            'max'      => 'Kolom :attribute tidak boleh lebih dari :max karakter.',
            'array'    => 'Format :attribute tidak valid.',
            'min'      => 'Minimal pilih :min item untuk :attribute.',
            'integer'  => 'Kolom :attribute harus berupa angka.',
            'exists'   => 'Data :attribute tidak ditemukan di database.',

            'list_tools.required' => 'Anda wajib memilih minimal satu alat.',
            'list_tools.*.id.exists' => 'Salah satu alat yang dipilih tidak valid.',
            'list_tools.*.qty.required' => 'Jumlah alat wajib diisi.',
            'list_tools.*.qty.min' => 'Jumlah alat minimal 1.',
        ];

        $attributes = [
            'nama_method' => 'Nama Metode',
            'list_tools'  => 'Daftar Alat',
            'organization_id' => 'Organisasi',
            'list_tools.*.id' => 'Alat',
            'list_tools.*.qty' => 'Jumlah',
        ];

        $validator = Validator::make($request->all(), [
            'nama_method' => 'required|string|max:255',
            'list_tools'  => 'required|array|min:1',
            'list_tools.*.id' => 'required|integer|exists:tools,tool_id',
            'list_tools.*.qty' => 'required|integer|min:1',
            'organization_id' => Auth::user()->role_id == 1 ? 'required|exists:organizations,organization_id' : 'nullable'
        ], $messages, $attributes);

        $validator->after(function ($validator) use ($request) {
            $listTools = $request->list_tools ?? [];
            foreach ($listTools as $index => $item) {
                if (isset($item['id']) && isset($item['qty'])) {
                    $tool = Tool::find($item['id']);
                    if ($tool) {
                        if ($item['qty'] > $tool->jumlah) {
                            $validator->errors()->add(
                                "list_tools.$index.qty",
                                "Stok tidak cukup. {$tool->nama} hanya sisa {$tool->jumlah}."
                            );
                        }
                    }
                }
            }
        });

        return $validator;
    }
}
