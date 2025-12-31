<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $limit = $request->input('limit', 10);

        $query = Organization::withCount([
            'users as users_count' => function ($q) {
                $q->where('role_id', '!=', 1);
            }
        ]);

        if ($keyword) {
            $query->where('organization_name', 'like', '%' . $keyword . '%');
        }

        $query->orderBy('organization_name', 'asc');
        $organizations = $query->paginate($limit);

        $organizations->appends($request->only(['keyword', 'limit']));

        if ($request->ajax()) {
            return view('organization.partials.table_data', [
                'organizations' => $organizations
            ]);
        }

        return view('organization.list', [
            'organizations' => $organizations,
            'keyword'       => $keyword,
            'limit'         => $limit,
        ]);
    }

    public function show(Organization $organization)
    {
        return $organization;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organization_name' => 'required|string|max:255|unique:organizations',
        ], [
            'organization_name.required' => 'Nama organisasi wajib diisi.',
            'organization_name.unique' => 'Nama organisasi sudah ada.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $organization = Organization::create([
            'organization_name' => $request->organization_name,
        ]);

        $ewsOptionId = 4;
        $option = Option::find($ewsOptionId);

        if ($option) {
            $currentData = $option->text_value ?? [];

            $defaultEwsData = [
                'keparahan' => [
                    'low'    => "365",
                    'medium' => "180",
                    'high'   => "90"
                ],
                'geografis' => [
                    'low'    => "10",
                    'medium' => "20",
                    'high'   => "30"
                ],
                'usia' => [
                    'low'    => "10",
                    'medium' => "20",
                    'high'   => "30"
                ],
                'material' => [
                    'low'    => "10",
                    'medium' => "20",
                    'high'   => "30"
                ]
            ];

            $currentData[$organization->organization_id] = $defaultEwsData;

            $option->text_value = $currentData;
            $option->save();
        }

        return response()->json(['success' => 'Data organisasi berhasil ditambahkan.']);
    }

    public function update(Request $request, Organization $organization)
    {
        $validator = Validator::make($request->all(), [
            'organization_name' => 'required|string|max:255|unique:organizations,organization_name,' . $organization->organization_id . ',organization_id',
        ], [
            'organization_name.required' => 'Nama organisasi wajib diisi.',
            'organization_name.unique' => 'Nama organisasi sudah ada.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $organization->update([
            'organization_name' => $request->organization_name,
        ]);

        return response()->json(['success' => 'Data organisasi berhasil diperbarui.']);
    }

    public function destroy(Organization $organization)
    {
        try {
            $ewsOptionId = 4;
            $option = Option::find($ewsOptionId);

            if ($option && !empty($option->text_value)) {
                $currentData = $option->text_value;
                $orgId = $organization->organization_id;

                if (isset($currentData[$orgId])) {
                    unset($currentData[$orgId]);

                    $option->text_value = $currentData;
                    $option->save();
                }
            }

            $organization->delete();

            return response()->json(['success' => 'Data organisasi berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus data. Data mungkin terkait dengan data lain.'], 500);
        }
    }

    public function getUsers(Organization $organization)
    {
        $users = $organization->users()
            ->where('role_id', '!=', 1)
            ->orderBy('role_id', 'asc')
            ->paginate(5);

        return view('organization.partials.users_table', [
            'users' => $users,
            'organization' => $organization
        ]);
    }
}
