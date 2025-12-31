<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.sign-in');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|numeric',
            'password' => 'required|string',
        ], [
            'login.required' => 'NIP / No. HP wajib diisi.',
            'login.numeric' => 'NIP / No. HP harus berupa angka.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('nip', $request->login)
            ->orWhere('no_hp', $request->login)
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'login' => 'NIP / No. HP tidak terdaftar.',
            ]);
        }
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => 'Password yang Anda masukkan salah.',
            ]);
        }
        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'login' => 'Akun Anda telah dinonaktifkan.',
            ]);
        }

        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();
        $request->session()->put('user_data', [
            'user_id' => $user->user_id,
            'organization_id' => $user->organization_id,
            'name' => $user->name,
            'nip' => $user->nip,
            'role_id' => $user->role_id,
            'short_role_name' => match ((int) $user->role_id) {
                1 => 'super',
                2 => 'admin',
                3 => 'asman',
                4 => 'team-leader',
                5 => 'jtc',
                default => null,
            },
        ]);


        switch ($user->role_id) {
            case 1: // Super Admin
                return redirect()->intended(route('super.dashboard'));
            case 2: // Admin Sistem
                return redirect()->intended(route('admin.dashboard'));
            case 3: // Assistant Manager
                return redirect()->intended(route('asman.dashboard'));
            case 4: // Team Leader
                return redirect()->intended(route('team-leader.dashboard'));
            case 5: // JTC
                return redirect()->intended(route('jtc.dashboard'));
            default:
                Auth::logout();
                return redirect()->route('login')->withErrors(['login' => 'Role tidak valid.']);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function profile()
    {
        $user = User::with(['organization', 'role', 'certificates'])->find(Auth::id());
        return view('setting.profile', compact('user'));
    }

    public function updateGeneral(Request $request)
    {
        $user = User::find(Auth::id());

        $rules = [
            'name'   => 'required|string|max:255',
            'nip'    => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('users', 'nip')->ignore($user->user_id, 'user_id'),
                'required_without:no_hp'
            ],
            'no_hp'  => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'no_hp')->ignore($user->user_id, 'user_id'),
                'required_without:nip'
            ],
            'alamat' => 'nullable|string',
            'foto'   => 'nullable|image|mimes:jpeg,png,jpg|max:15360',
        ];

        $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            'nip.unique' => 'NIP sudah terdaftar di sistem.',
            'nip.max' => 'NIP maksimal 50 karakter.',
            'nip.required_without' => 'NIP atau Nomor HP harus diisi salah satu.',
            'no_hp.unique' => 'Nomor HP sudah terdaftar di sistem.',
            'no_hp.required_without' => 'Nomor HP atau NIP harus diisi salah satu.',
            'no_hp.max' => 'Nomor HP maksimal 20 karakter.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format foto harus: jpeg, png, jpg.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $user->name   = $request->name;
            $user->nip    = $request->nip;
            $user->no_hp  = $request->no_hp;
            $user->alamat = $request->alamat;

            if ($request->input('foto_remove') == '1' && $user->foto) {
                if (Storage::disk('public')->exists($user->foto)) {
                    Storage::disk('public')->delete($user->foto);
                }
                $user->foto = null;
            }

            if ($request->hasFile('foto')) {
                if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                    Storage::disk('public')->delete($user->foto);
                }
                $user->foto = $request->file('foto')->store('users/photos', 'public');
            }

            if ($user->role_id == 3) {
                if ($request->hasFile('signature')) {
                    if ($user->signature && Storage::disk('public')->exists($user->signature)) {
                        Storage::disk('public')->delete($user->signature);
                    }
                    $user->signature = $request->file('signature')->store('users/signatures', 'public');
                }
            }

            $user->save();

            $this->refreshSessionData($request, $user);

            return response()->json(['message' => 'Data umum berhasil diperbarui.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal update: ' . $e->getMessage()], 500);
        }
    }

    public function updateSignature(Request $request)
    {
        $user = User::find(Auth::id());

        $validator = Validator::make($request->all(), [
            'signature_file'   => 'nullable|image|mimes:jpeg,png,jpg|max:15360',
            'signature_canvas' => 'nullable|string',
            'signature_remove' => 'nullable|in:0,1',
        ], [
            'signature_file.image' => 'File harus berupa gambar.',
            'signature_file.mimes' => 'Format file harus jpeg, png, atau jpg.',
            'signature_file.max'   => 'Ukuran file maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            if ($request->input('signature_remove') == '1' && !$request->hasFile('signature_file') && !$request->filled('signature_canvas')) {
                if ($user->signature && Storage::disk('public')->exists($user->signature)) {
                    Storage::disk('public')->delete($user->signature);
                }
                $user->signature = null;
            }

            if ($request->hasFile('signature_file')) {
                if ($user->signature && Storage::disk('public')->exists($user->signature)) {
                    Storage::disk('public')->delete($user->signature);
                }

                $path = $request->file('signature_file')->store('users/signatures', 'public');
                $user->signature = $path;
            }
            elseif ($request->filled('signature_canvas')) {
                if ($user->signature && Storage::disk('public')->exists($user->signature)) {
                    Storage::disk('public')->delete($user->signature);
                }

                $image_64 = $request->signature_canvas;

                if (strpos($image_64, ',') !== false) {
                    @list($type, $data) = explode(';', $image_64);
                    @list(, $data)      = explode(',', $data);
                    $image_64           = $data;
                }

                $image_content = base64_decode($image_64);

                if ($image_content === false) {
                    throw new \Exception('Gagal memproses gambar canvas.');
                }

                $fileName = 'users/signatures/sig_' . time() . '_' . Str::random(10) . '.png';

                Storage::disk('public')->put($fileName, $image_content);
                $user->signature = $fileName;
            }

            $user->save();

            return response()->json(['message' => 'Tanda tangan berhasil diperbarui.', 'reload' => true]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyimpan tanda tangan: ' . $e->getMessage()], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        $user = User::find(Auth::id());

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:6|confirmed|different:current_password',
        ], [
            'current_password.current_password' => 'Password lama salah.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.different' => 'Password baru tidak boleh sama dengan password lama.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json(['message' => 'Password berhasil diubah.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal update password.'], 500);
        }
    }

    public function updateCertificates(Request $request)
    {
        $user = User::find(Auth::id());

        $rules = [
            'certificates' => 'nullable|array',
            'certificates_to_delete' => 'nullable|string',
        ];

        if ($request->has('certificates')) {
            foreach ($request->input('certificates') as $key => $val) {
                if (str_starts_with($key, 'new_')) {
                    $rules["certificates.$key.file"] = 'required|file|mimes:pdf,jpg,jpeg,png|max:15360';
                } else {
                    $rules["certificates.$key.file"] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:15360';
                }
                $rules["certificates.$key.expired_date"] = 'nullable|date';
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            if ($request->filled('certificates_to_delete')) {
                $idsToDelete = explode(',', $request->certificates_to_delete);
                $certsToDelete = Certificate::whereIn('certificate_id', $idsToDelete)
                    ->where('user_id', $user->user_id)
                    ->get();

                foreach ($certsToDelete as $cert) {
                    if (Storage::disk('public')->exists($cert->file)) {
                        Storage::disk('public')->delete($cert->file);
                    }
                    $cert->delete();
                }
            }

            if ($request->has('certificates')) {
                foreach ($request->certificates as $key => $data) {

                    if (str_starts_with($key, 'new_') && $request->hasFile("certificates.$key.file")) {
                        $file = $request->file("certificates.$key.file");
                        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs('users/certificates', $fileName, 'public');

                        Certificate::create([
                            'user_id' => $user->user_id,
                            'file' => $path,
                            'expired_date' => $data['expired_date'] ?? null,
                        ]);
                    } elseif (str_starts_with($key, 'existing_')) {
                        $certId = str_replace('existing_', '', $key);
                        $cert = Certificate::where('certificate_id', $certId)
                            ->where('user_id', $user->user_id)->first();

                        if ($cert) {
                            $cert->expired_date = $data['expired_date'] ?? null;

                            if ($request->hasFile("certificates.$key.file")) {
                                if (Storage::disk('public')->exists($cert->file)) {
                                    Storage::disk('public')->delete($cert->file);
                                }
                                $file = $request->file("certificates.$key.file");
                                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                                $path = $file->storeAs('users/certificates', $fileName, 'public');
                                $cert->file = $path;
                            }
                            $cert->save();
                        }
                    }
                }
            }

            DB::commit();
            return response()->json(['message' => 'Data sertifikat berhasil diperbarui.', 'reload' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memproses sertifikat: ' . $e->getMessage()], 500);
        }
    }

    private function refreshSessionData(Request $request, User $user)
    {
        $request->session()->put('user_data', [
            'user_id' => $user->user_id,
            'organization_id' => $user->organization_id,
            'name' => $user->name,
            'nip' => $user->nip,
            'role_id' => $user->role_id,
            'short_role_name' => session('user_data.short_role_name'),
        ]);
    }
}
