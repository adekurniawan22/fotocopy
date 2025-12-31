<?php

namespace App\Http\Controllers;


use App\Exports\UserExport;
use App\Models\Certificate;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $roleId = $request->input('role_id');
        $limit = $request->input('limit', 10);

        $query = User::with(['organization', 'role'])->withCount('certificates')->where('role_id', '!=', 1);

        $currentRole = session('user_data.role_id');
        $currentOrgId = session('user_data.organization_id');

        if ($currentRole != 1) {
            $query->where('organization_id', $currentOrgId);
        }

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('nip', 'like', '%' . $keyword . '%')
                    ->orWhere('no_hp', 'like', '%' . $keyword . '%');
            });
        }

        if ($roleId) {
            $query->where('role_id', $roleId);
        }

        $query->orderBy('name', 'asc');
        $users = $query->paginate($limit);
        $users->appends($request->only(['keyword', 'limit', 'role_id']));

        if ($request->ajax()) {
            return view('user.partials.table_data', compact('users'));
        }

        if ($currentRole != 1) {
            $organizations = Organization::where('organization_id', $currentOrgId)
                ->orderBy('organization_name', 'asc')
                ->get(['organization_id', 'organization_name']);
        } else {
            $organizations = Organization::orderBy('organization_name', 'asc')
                ->get(['organization_id', 'organization_name']);
        }

        $roles = Role::where('role_id', '!=', 1)
            ->orderBy('role_name', 'asc')
            ->get(['role_id', 'role_name']);

        return view('user.list', compact('users', 'keyword', 'limit', 'roles', 'organizations'));
    }

    public function show($id)
    {
        $user = User::with('certificates')->findOrFail($id);

        $certificates = $user->certificates->map(function ($cert) {
            return [
                'certificate_id' => $cert->certificate_id,
                'expired_date' => $cert->expired_date,
                'file_url' => $cert->file_url,
                'file_name' => $cert->file_name,
            ];
        });

        return response()->json([
            'user' => $user,
            'foto_url' => $user->foto
                ? asset('storage/' . $user->foto)
                : null,
            'signature_url' => $user->signature
                ? asset('storage/' . $user->signature)
                : null,
            'certificates' => $certificates,
        ]);
    }

    public function store(Request $request)
    {
        $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            'nip.unique' => 'NIP sudah terdaftar di sistem.',
            'nip.max' => 'NIP maksimal 50 karakter.',
            'nip.required_without' => 'NIP atau Nomor HP harus diisi salah satu.',
            'no_hp.unique' => 'Nomor HP sudah terdaftar di sistem.',
            'no_hp.required_without' => 'Nomor HP atau NIP harus diisi salah satu.',
            'no_hp.max' => 'Nomor HP maksimal 20 karakter.',
            'role_id.required' => 'Role/Peran wajib dipilih.',
            'role_id.exists' => 'Role yang dipilih tidak valid.',
            'organization_id.required' => 'Organisasi wajib dipilih.',
            'organization_id.exists' => 'Organisasi yang dipilih tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',

            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format foto harus: jpeg, png, jpg.',

            'signature.image' => 'File tanda tangan harus berupa gambar.',
            'signature.mimes' => 'Format tanda tangan harus: jpeg, png, jpg.',

            'certificates.new_*.file.required' => 'File sertifikat wajib diunggah.',
            'certificates.new_*.file.file' => 'File sertifikat korup atau gagal terunggah.',
            'certificates.new_*.file.mimes' => 'Format sertifikat harus: pdf, jpg, png, jpeg.',
            'certificates.new_*.expired_date.date' => 'Format tanggal kadaluarsa tidak valid.',
        ];

        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'nip'   => 'required_without:no_hp|nullable|string|max:50|unique:users,nip',
            'no_hp' => 'required_without:nip|nullable|string|max:20|unique:users,no_hp',

            'role_id' => 'required|exists:roles,role_id',
            'organization_id' => 'required|exists:organizations,organization_id',
            'alamat' => 'nullable|string',
            'password' => 'required|string|min:6|confirmed',
            'is_active' => 'nullable|boolean',

            'foto' => 'nullable|image|mimes:jpeg,png,jpg',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg',

            'certificates' => 'nullable|array',
            'certificates.new_*.file' => 'required|file|mimes:pdf,jpg,png,jpeg',
            'certificates.new_*.expired_date' => 'nullable|date',
        ], $messages);

        $limitError = $this->checkRoleLimit($validated['organization_id'], $validated['role_id']);
        if ($limitError) {
            return response()->json([
                'message' => 'Validasi Gagal',
                'errors' => ['role_id' => [$limitError]]
            ], 422);
        }

        DB::beginTransaction();
        try {
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('users/photos', 'public');
            }

            $signaturePath = null;
            if ($request->hasFile('signature')) {
                $signaturePath = $request->file('signature')->store('users/signatures', 'public');
            }

            $user = User::create([
                'name' => $validated['name'],
                'nip' => $validated['nip'] ?? null,
                'no_hp' => $validated['no_hp'] ?? null,
                'role_id' => $validated['role_id'],
                'organization_id' => $validated['organization_id'],
                'alamat' => $validated['alamat'] ?? null,
                'password' => Hash::make($validated['password']),
                'is_active' => $request->input('is_active', 1),
                'foto' => $fotoPath,
                'signature' => $signaturePath,
            ]);

            if ($request->has('certificates')) {
                foreach ($request->certificates as $key => $certData) {
                    if (strpos($key, 'new_') === 0 && $request->hasFile("certificates.{$key}.file")) {
                        $file = $request->file("certificates.{$key}.file");
                        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                        $filePath = $file->storeAs('users/certificates', $fileName, 'public');

                        Certificate::create([
                            'user_id' => $user->user_id,
                            'file' => $filePath,
                            'expired_date' => $certData['expired_date'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => 'Data personil berhasil ditambahkan.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($fotoPath) && $fotoPath && Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }
            if (isset($signaturePath) && $signaturePath && Storage::disk('public')->exists($signaturePath)) {
                Storage::disk('public')->delete($signaturePath);
            }

            return response()->json([
                'error' => 'Gagal menyimpan data personil. ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            'nip.unique' => 'NIP sudah digunakan oleh pengguna lain.',
            'nip.max' => 'NIP maksimal 50 karakter.',
            'nip.required_without' => 'NIP atau Nomor HP harus diisi salah satu.',
            'no_hp.unique' => 'Nomor HP sudah terdaftar di sistem.',
            'no_hp.required_without' => 'Nomor HP atau NIP harus diisi salah satu.',
            'no_hp.max' => 'Nomor HP maksimal 20 karakter.',
            'role_id.required' => 'Role/Peran wajib dipilih.',
            'role_id.exists' => 'Role yang dipilih tidak valid.',
            'organization_id.required' => 'Organisasi wajib dipilih.',
            'organization_id.exists' => 'Organisasi yang dipilih tidak valid.',

            'password.min' => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',

            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format foto harus: jpeg, png, jpg.',

            'signature.image' => 'File tanda tangan harus berupa gambar.',
            'signature.mimes' => 'Format tanda tangan harus: jpeg, png, jpg.',

            'certificates.new_*.file.required' => 'File sertifikat baru wajib diunggah.',
            'certificates.new_*.file.mimes' => 'Format sertifikat baru harus: pdf, jpg, png, jpeg.',

            'certificates.existing_*.file.mimes' => 'Format sertifikat pengganti harus: pdf, jpg, png, jpeg.',

            'certificates.new_*.expired_date.date' => 'Format tanggal kadaluarsa tidak valid.',
            'certificates.existing_*.expired_date.date' => 'Format tanggal kadaluarsa tidak valid.',
        ];

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nip' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('users', 'nip')->ignore($user->user_id, 'user_id'),
            ],
            'no_hp'  => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'no_hp')->ignore($user->user_id, 'user_id'),
                'required_without:nip'
            ],
            'role_id' => 'required|exists:roles,role_id',
            'organization_id' => 'required|exists:organizations,organization_id',
            'alamat' => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed',
            'is_active' => 'nullable|boolean',

            'foto' => 'nullable|image|mimes:jpeg,png,jpg',
            'foto_remove' => 'nullable|string',

            'signature' => 'nullable|image|mimes:jpeg,png,jpg',
            'signature_remove' => 'nullable|string',

            'certificates' => 'nullable|array',
            'certificates.new_*.file' => 'required|file|mimes:pdf,jpg,png,jpeg',
            'certificates.new_*.expired_date' => 'nullable|date',
            'certificates.existing_*.file' => 'nullable|file|mimes:pdf,jpg,png,jpeg',
            'certificates.existing_*.expired_date' => 'nullable|date',
            'certificates_to_delete' => 'nullable|array',
            'certificates_to_delete.*' => 'integer|exists:certificates,certificate_id',

        ], $messages);

        $limitError = $this->checkRoleLimit($validated['organization_id'], $validated['role_id']);
        if ($limitError) {
            return response()->json([
                'message' => 'Validasi Gagal',
                'errors' => ['role_id' => [$limitError]]
            ], 422);
        }

        DB::beginTransaction();
        try {
            $fotoPath = $user->foto;
            if ($request->filled('foto_remove') && $user->foto) {
                if (Storage::disk('public')->exists($user->foto)) {
                    Storage::disk('public')->delete($user->foto);
                }
                $fotoPath = null;
            }
            if ($request->hasFile('foto')) {
                if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                    Storage::disk('public')->delete($user->foto);
                }
                $fotoPath = $request->file('foto')->store('users/photos', 'public');
            }

            $signaturePath = $user->signature;
            if ($request->filled('signature_remove') && $user->signature) {
                if (Storage::disk('public')->exists($user->signature)) {
                    Storage::disk('public')->delete($user->signature);
                }
                $signaturePath = null;
            }
            if ($request->hasFile('signature')) {
                if ($user->signature && Storage::disk('public')->exists($user->signature)) {
                    Storage::disk('public')->delete($user->signature);
                }
                $signaturePath = $request->file('signature')->store('users/signatures', 'public');
            }

            $user->update([
                'name' => $validated['name'],
                'nip' => $validated['nip'] ?? null,
                'no_hp' => $validated['no_hp'] ?? null,
                'role_id' => $validated['role_id'],
                'organization_id' => $validated['organization_id'],
                'alamat' => $validated['alamat'] ?? null,
                'is_active' => $request->input('is_active', $user->is_active),
                'foto' => $fotoPath,
                'signature' => $signaturePath,
            ]);

            if ($request->filled('password')) {
                $user->update([
                    'password' => Hash::make($validated['password'])
                ]);
            }

            if ($request->filled('certificates_to_delete')) {
                $certsToDelete = Certificate::whereIn('certificate_id', $request->certificates_to_delete)
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
                foreach ($request->certificates as $key => $certData) {
                    if (strpos($key, 'new_') === 0 && $request->hasFile("certificates.{$key}.file")) {
                        $file = $request->file("certificates.{$key}.file");
                        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                        $filePath = $file->storeAs('users/certificates', $fileName, 'public');

                        Certificate::create([
                            'user_id' => $user->user_id,
                            'file' => $filePath,
                            'expired_date' => $certData['expired_date'] ?? null,
                        ]);
                    }

                    if (strpos($key, 'existing_') === 0) {
                        $certId = (int)str_replace('existing_', '', $key);
                        $certificate = Certificate::where('certificate_id', $certId)
                            ->where('user_id', $user->user_id)
                            ->first();

                        if ($certificate) {
                            if ($request->hasFile("certificates.{$key}.file")) {
                                $file = $request->file("certificates.{$key}.file");

                                if (Storage::disk('public')->exists($certificate->file)) {
                                    Storage::disk('public')->delete($certificate->file);
                                }

                                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                                $filePath = $file->storeAs('users/certificates', $fileName, 'public');
                                $certificate->file = $filePath;
                            }

                            $certificate->expired_date = $certData['expired_date'] ?? null;
                            $certificate->save();
                        }
                    }
                }
            }

            DB::commit();

            if (Auth::check() && $user->user_id == Auth::id()) {
                $user->refresh();

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

                return response()->json([
                    'success' => 'Data session berhasil diperbarui.'
                ]);
            }

            return response()->json([
                'success' => 'Data personil berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Gagal memperbarui data personil. ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $user = User::with('certificates')->findOrFail($id);

            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            if ($user->signature && Storage::disk('public')->exists($user->signature)) {
                Storage::disk('public')->delete($user->signature);
            }

            $certificateFiles = $user->certificates->pluck('file')->filter();

            $user->certificates()->delete();

            foreach ($certificateFiles as $filePath) {
                if ($filePath && Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }

            $user->delete();

            DB::commit();

            return response()->json([
                'success' => 'Data personil dan berkas terkait berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Gagal menghapus data personil. ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $keyword = $request->input('keyword');
        $roleId = $request->input('role_id');

        $waktuIndonesia = now()->setTimezone('Asia/Jakarta')->format('Y-m-d H.i');
        $fileName = "Data_Personil_{$waktuIndonesia}.xlsx";

        return Excel::download(new UserExport($keyword, $roleId), $fileName);
    }

    public function updateStatus(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        try {
            $user->update([
                'is_active' => $request->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status user berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status user.'
            ], 500);
        }
    }

    private function canManageCertificate($targetUserId)
    {
        $currentUser = Auth::user();

        if (!$currentUser) {
            return false;
        }

        return $currentUser->role_id == 1 || $currentUser->user_id == $targetUserId;
    }

    public function certificatePage($userId)
    {
        $user = User::with('certificates')->findOrFail($userId);
        return view('user.certificate', compact('user'));
    }

    public function storeSingleCertificate(Request $request, $userId)
    {
        if (!$this->canManageCertificate($userId)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,png,jpeg',
            'expired_date' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('users/certificates', $fileName, 'public');

            Certificate::create([
                'user_id' => $userId,
                'file' => $filePath,
                'expired_date' => $request->expired_date,
            ]);

            DB::commit();
            return response()->json(['success' => 'Sertifikat berhasil ditambahkan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }
    }

    public function updateSingleCertificate(Request $request, $userId, $certificateId)
    {
        if (!$this->canManageCertificate($userId)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $certificate = Certificate::where('user_id', $userId)
            ->where('certificate_id', $certificateId)
            ->firstOrFail();

        $request->validate([
            'file' => 'nullable|file|mimes:pdf,jpg,png,jpeg',
            'expired_date' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('file')) {
                if (Storage::disk('public')->exists($certificate->file)) {
                    Storage::disk('public')->delete($certificate->file);
                }
                $file = $request->file('file');
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('users/certificates', $fileName, 'public');
                $certificate->file = $filePath;
            }

            $certificate->expired_date = $request->expired_date;
            $certificate->save();

            DB::commit();
            return response()->json(['success' => 'Sertifikat berhasil diperbarui.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal update: ' . $e->getMessage()], 500);
        }
    }

    public function destroySingleCertificate($userId, $certificateId)
    {
        if (!$this->canManageCertificate($userId)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $certificate = Certificate::where('user_id', $userId)
            ->where('certificate_id', $certificateId)
            ->firstOrFail();

        try {
            if (Storage::disk('public')->exists($certificate->file)) {
                Storage::disk('public')->delete($certificate->file);
            }
            $certificate->delete();

            return response()->json(['success' => 'Sertifikat berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal hapus: ' . $e->getMessage()], 500);
        }
    }

    private function checkRoleLimit($organizationId, $roleId, $excludeUserId = null)
    {
        $limits = [
            2 => 1,
            3 => 1,
            4 => 2,
        ];

        if (!array_key_exists($roleId, $limits)) {
            return null;
        }

        $maxLimit = $limits[$roleId];

        $query = User::where('organization_id', $organizationId)
            ->where('role_id', $roleId)
            ->where('is_active', 1);

        if ($excludeUserId) {
            $query->where('user_id', '!=', $excludeUserId);
        }

        $currentCount = $query->count();

        if ($currentCount >= $maxLimit) {
            return "Kuota untuk Role ini pada Organisasi tersebut sudah penuh (Maksimal: $maxLimit).";
        }

        return null;
    }
}
