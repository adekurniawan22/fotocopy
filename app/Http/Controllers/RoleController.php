<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->get();

        return view('role.list', [
            'roles' => $roles,
        ]);
    }

    public function show(Role $role)
    {
        return $role;
    }

    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:255|unique:roles,role_name,' . $role->role_id . ',role_id',
        ], [
            'role_name.required' => 'Nama jabatan wajib diisi.',
            'role_name.unique' => 'Nama jabatan sudah ada.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role->update([
            'role_name' => $request->role_name,
        ]);

        return response()->json(['success' => 'Data jabatan berhasil diperbarui.']);
    }
}
