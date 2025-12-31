<?php

namespace App\Http\Controllers;

use App\Models\Partnership;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Exports\PartnershipExport;
use Maatwebsite\Excel\Facades\Excel;

class PartnershipController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $limit = $request->input('limit', 10);

        $query = Partnership::with('organization');

        $currentRole = session('user_data.role_id');
        $currentOrgId = session('user_data.organization_id');

        if ($currentRole != 1) {
            $query->where('organization_id', $currentOrgId);
        }

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('partnership_name', 'like', '%' . $keyword . '%')
                    ->orWhere('alamat', 'like', '%' . $keyword . '%')
                    ->orWhere('penanggung_jawab', 'like', '%' . $keyword . '%')
                    ->orWhere('no_hp', 'like', '%' . $keyword . '%');
            });
        }

        $query->orderBy('partnership_name', 'asc');
        $partnerships = $query->paginate($limit);
        $partnerships->appends($request->only(['keyword', 'limit']));

        if ($request->ajax()) {
            return view('partnership.partials.table_data', compact('partnerships'));
        }

        $organizations = Organization::orderBy('organization_name', 'asc')->get();

        return view('partnership.list', compact('partnerships', 'keyword', 'organizations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'partnership_name' => 'required|string|max:255',
            'penanggung_jawab' => 'required|string|max:255',
            'organization_id' => 'required|exists:organizations,organization_id',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
        ]);

        Partnership::create($validated);

        return response()->json(['success' => 'Data Partnership berhasil ditambahkan.']);
    }

    public function show($id)
    {
        $partnership = Partnership::findOrFail($id);
        return response()->json($partnership);
    }

    public function update(Request $request, $id)
    {
        $partnership = Partnership::findOrFail($id);

        $validated = $request->validate([
            'partnership_name' => 'required|string|max:255',
            'penanggung_jawab' => 'required|string|max:255',
            'organization_id' => 'required|exists:organizations,organization_id',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
        ]);

        $partnership->update($validated);

        return response()->json(['success' => 'Data Partnership berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $partnership = Partnership::findOrFail($id);
        $partnership->delete();

        return response()->json(['success' => 'Data Partnership berhasil dihapus.']);
    }

    public function export(Request $request)
    {
        $keyword = $request->input('keyword');

        $waktuIndonesia = now()->setTimezone('Asia/Jakarta')->format('Y-m-d H.i');
        $fileName = "Data_Partnership_{$waktuIndonesia}.xlsx";

        return Excel::download(new PartnershipExport($keyword), $fileName);
    }
}
