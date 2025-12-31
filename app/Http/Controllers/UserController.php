<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(6);

        if ($request->ajax()) {
            return view('users.partials.table_data', compact('users'))->render();
        }

        return view('users.list', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'user_name' => 'required|string|max:255|unique:users,user_name',
            'password'  => 'required|string|min:6',
        ]);

        User::create($validated);

        return response()->json(['success' => 'Data user berhasil ditambahkan.']);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'name'      => 'required|string|max:255',
            'user_name' => 'required|string|max:255|unique:users,user_name,' . $id . ',user_id',
            'password'  => 'nullable|string|min:6',
        ];

        $validated = $request->validate($rules);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json(['success' => 'Data user berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => 'Data user berhasil dihapus.']);
    }
}
