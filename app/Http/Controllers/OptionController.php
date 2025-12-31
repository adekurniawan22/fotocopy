<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class OptionController extends Controller
{
    private const OPTION_HISTORY_TOOL_ID = 1;
    private const OPTION_SPKI_ID = 2;
    private const OPTION_LAPORAN_PEKERJAAN_ID = 3;
    private const OPTION_EWS_CALCULATION_ID = 4;

    public function viewSettingHistoryTool()
    {
        $user = Auth::user();
        $organizations = [];
        $data = [];

        $option = Option::find(self::OPTION_HISTORY_TOOL_ID);
        $allData = $option ? $option->text_value : [];

        if ($user->role_id == 1) {
            $organizations = Organization::all();
            $data = [];
        } else {
            $orgId = session('user_data.organization_id') ?? $user->organization_id;
            $data = $allData[$orgId] ?? [];
        }

        return view('setting.history-tool', compact('data', 'organizations'));
    }

    public function getSettingHistoryTool(Request $request)
    {
        $orgId = $request->query('organization_id');

        $option = Option::find(self::OPTION_HISTORY_TOOL_ID);
        $allData = $option ? $option->text_value : [];
        $data = $allData[$orgId] ?? [];

        if (!empty($data['logo_pdf'])) {
            $data['logo_url'] = asset('storage/' . $data['logo_pdf']);
        }

        return response()->json($data);
    }

    public function updateSettingHistoryTool(Request $request)
    {
        return $this->processUpdate($request, self::OPTION_HISTORY_TOOL_ID);
    }

    public function viewSettingSPKI()
    {
        $user = Auth::user();
        $organizations = [];
        $data = [];

        $option = Option::find(self::OPTION_SPKI_ID);
        $allData = $option ? $option->text_value : [];

        if ($user->role_id == 1) {
            $organizations = Organization::all();
            $data = [];
        } else {
            $orgId = session('user_data.organization_id') ?? $user->organization_id;
            $data = $allData[$orgId] ?? [];
        }

        return view('setting.spki', compact('data', 'organizations'));
    }

    public function getSettingSPKI(Request $request)
    {
        $orgId = $request->query('organization_id');

        $option = Option::find(self::OPTION_SPKI_ID);
        $allData = $option ? $option->text_value : [];
        $data = $allData[$orgId] ?? [];

        if (!empty($data['logo_pdf'])) {
            $data['logo_url'] = asset('storage/' . $data['logo_pdf']);
        }

        return response()->json($data);
    }

    public function updateSettingSPKI(Request $request)
    {
        return $this->processUpdate($request, self::OPTION_SPKI_ID);
    }

    private function processUpdate(Request $request, int $optionId)
    {
        $user = Auth::user();

        if ($user->role_id == 1) {
            $orgId = $request->organization_id;
        } else {
            $orgId = session('user_data.organization_id') ?? $user->organization_id;
        }

        $option = Option::find($optionId);
        $allData = $option ? $option->text_value : [];
        $currentOrgData = $allData[$orgId] ?? [];

        $hasExistingLogo = !empty($currentOrgData['logo_pdf']);
        $isRemovingLogo = $request->input('avatar_remove') == '1';

        $rules = [
            'judul_pdf' => 'required|string|max:255',
        ];

        if (!$hasExistingLogo || $isRemovingLogo) {
            $rules['logo_pdf'] = 'required|image|mimes:jpeg,png,jpg,svg|max:15360';
        } else {
            $rules['logo_pdf'] = 'nullable|image|mimes:jpeg,png,jpg,svg|max:15360';
        }

        if ($user->role_id == 1) {
            $rules['organization_id'] = 'required|exists:organizations,organization_id';
        }

        $messages = [
            'required' => ':attribute wajib diisi.',
            'string'   => ':attribute harus berupa teks.',
            'image'    => ':attribute harus berupa file gambar.',
            'mimes'    => 'Format :attribute harus berupa: :values.',
            'max'      => [
                'string' => ':attribute tidak boleh lebih dari :max karakter.',
                'file'   => 'Ukuran :attribute tidak boleh lebih dari 5MB.',
            ],
            'exists'   => ':attribute yang dipilih tidak valid.',
        ];

        $attributes = [
            'organization_id' => 'Organisasi',
            'judul_pdf'       => 'Judul Kop PDF',
            'logo_pdf'        => 'Logo PDF',
        ];

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $logoPath = $currentOrgData['logo_pdf'] ?? null;

        if ($request->hasFile('logo_pdf')) {
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }

            $logoPath = $request->file('logo_pdf')->store('settings', 'public');
        } elseif ($request->input('avatar_remove') == '1') {
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            $logoPath = null;
        }

        $newOrgData = [
            'organization_id' => (string)$orgId,
            'judul_pdf'       => $request->judul_pdf,
            'logo_pdf'        => $logoPath,
        ];

        $allData[$orgId] = $newOrgData;

        if (!$option) {
            $option = new Option();
            $option->id = $optionId;
        }

        $option->text_value = $allData;
        $option->save();

        return redirect()->back()
            ->with('success', 'Pengaturan berhasil disimpan.')
            ->withInput(['organization_id' => $orgId]);
    }

    public function viewSettingLaporanPekerjaan()
    {
        $user = Auth::user();
        $organizations = [];
        $data = [];

        $option = Option::find(self::OPTION_LAPORAN_PEKERJAAN_ID);
        $allData = $option ? $option->text_value : [];

        if ($user->role_id == 1) {
            $organizations = Organization::all();
            $data = [];
        } else {
            $orgId = session('user_data.organization_id') ?? $user->organization_id;
            $data = $allData[$orgId] ?? [];
        }

        return view('setting.laporan-pekerjaan', compact('data', 'organizations'));
    }

    public function getSettingLaporanPekerjaan(Request $request)
    {
        $orgId = $request->query('organization_id');

        $option = Option::find(self::OPTION_LAPORAN_PEKERJAAN_ID);
        $allData = $option ? $option->text_value : [];
        $data = $allData[$orgId] ?? [];

        if (!empty($data['foto_cover_pdf'])) {
            $data['foto_cover_url'] = asset('storage/' . $data['foto_cover_pdf']);
        }
        if (!empty($data['logo_header_isi'])) {
            $data['logo_header_url'] = asset('storage/' . $data['logo_header_isi']);
        }

        return response()->json($data);
    }

    public function updateSettingLaporanPekerjaan(Request $request)
    {
        $user = Auth::user();

        if ($user->role_id == 1) {
            $orgId = $request->organization_id;
        } else {
            $orgId = session('user_data.organization_id') ?? $user->organization_id;
        }

        $option = Option::find(self::OPTION_LAPORAN_PEKERJAAN_ID);
        $allData = $option ? $option->text_value : [];
        $currentOrgData = $allData[$orgId] ?? [];

        $hasExistingCover = !empty($currentOrgData['foto_cover_pdf']);
        $hasExistingLogo  = !empty($currentOrgData['logo_header_isi']);

        $rules = [
            'header_cover_pdf' => 'required|string|max:255',
            'footer_cover_pdf' => 'required|string|max:255',
            'header_isi'       => 'required|string|max:255',
            'penutup'          => 'required|string',
        ];

        if (!$hasExistingCover) {
            $rules['foto_cover_pdf'] = 'required|image|mimes:jpeg,png,jpg|max:15360';
        } else {
            $rules['foto_cover_pdf'] = 'nullable|image|mimes:jpeg,png,jpg|max:15360';
        }

        if (!$hasExistingLogo) {
            $rules['logo_header_isi'] = 'required|image|mimes:jpeg,png,jpg,svg|max:15360';
        } else {
            $rules['logo_header_isi'] = 'nullable|image|mimes:jpeg,png,jpg,svg|max:15360';
        }

        if ($user->role_id == 1) {
            $rules['organization_id'] = 'required|exists:organizations,organization_id';
        }

        $messages = [
            'required' => ':attribute wajib diisi.',
            'string'   => ':attribute harus berupa teks.',
            'image'    => ':attribute harus berupa file gambar.',
            'mimes'    => 'Format :attribute harus berupa: :values.',
            'exists'   => ':attribute yang dipilih tidak valid.',
            'max'      => [
                'string' => ':attribute tidak boleh lebih dari :max karakter.',
                'file'   => 'Ukuran :attribute tidak boleh lebih dari :max KB.',
            ],
        ];

        $attributes = [
            'organization_id'  => 'Organisasi',
            'header_cover_pdf' => 'Header Cover PDF',
            'foto_cover_pdf'   => 'Foto Cover PDF',
            'footer_cover_pdf' => 'Footer Cover PDF',
            'header_isi'       => 'Header Halaman Isi',
            'logo_header_isi'  => 'Logo Header Isi',
            'penutup'          => 'Kalimat Penutup',
        ];

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $fotoCoverPath = $currentOrgData['foto_cover_pdf'] ?? null;
        if ($request->hasFile('foto_cover_pdf')) {
            if ($fotoCoverPath && Storage::disk('public')->exists($fotoCoverPath)) {
                Storage::disk('public')->delete($fotoCoverPath);
            }
            $fotoCoverPath = $request->file('foto_cover_pdf')->store('settings/laporan', 'public');
        }

        $logoHeaderPath = $currentOrgData['logo_header_isi'] ?? null;
        if ($request->hasFile('logo_header_isi')) {
            if ($logoHeaderPath && Storage::disk('public')->exists($logoHeaderPath)) {
                Storage::disk('public')->delete($logoHeaderPath);
            }
            $logoHeaderPath = $request->file('logo_header_isi')->store('settings/laporan', 'public');
        }

        $newOrgData = [
            'organization_id'  => (string)$orgId,
            'header_cover_pdf' => $request->header_cover_pdf,
            'foto_cover_pdf'   => $fotoCoverPath,
            'footer_cover_pdf' => $request->footer_cover_pdf,
            'header_isi'       => $request->header_isi,
            'logo_header_isi'  => $logoHeaderPath,
            'penutup'          => $request->penutup,
        ];

        $allData[$orgId] = $newOrgData;

        if (!$option) {
            $option = new Option();
            $option->id = self::OPTION_LAPORAN_PEKERJAAN_ID;
        }

        $option->text_value = $allData;
        $option->save();

        return redirect()->back()
            ->with('success', 'Pengaturan Laporan Pekerjaan berhasil disimpan.')
            ->withInput(['organization_id' => $orgId]);
    }

    public function viewSettingEWS()
    {
        $user = Auth::user();
        $organizations = [];
        $data = [];

        $option = Option::find(self::OPTION_EWS_CALCULATION_ID);
        $allData = $option ? $option->text_value : [];

        if ($user->role_id == 1) {
            $organizations = Organization::all();
            $data = [];
        } else {
            $orgId = session('user_data.organization_id') ?? $user->organization_id;
            $data = $allData[$orgId] ?? [];
        }

        return view('setting.ews', compact('data', 'organizations'));
    }

    public function getSettingEWS(Request $request)
    {
        $orgId = $request->query('organization_id');

        $option = Option::find(self::OPTION_EWS_CALCULATION_ID);
        $allData = $option ? $option->text_value : [];

        $data = $allData[$orgId] ?? [];

        return response()->json($data);
    }

    public function updateSettingEWS(Request $request)
    {
        $user = Auth::user();

        if ($user->role_id == 1) {
            $orgId = $request->organization_id;
        } else {
            $orgId = session('user_data.organization_id') ?? $user->organization_id;
        }

        $rules = [
            'keparahan_low'    => 'required|numeric|min:0',
            'keparahan_medium' => 'required|numeric|min:0',
            'keparahan_high'   => 'required|numeric|min:0',

            'geo_low'    => 'required|numeric|min:0',
            'geo_medium' => 'required|numeric|min:0',
            'geo_high'   => 'required|numeric|min:0',

            'usia_low'    => 'required|numeric|min:0',
            'usia_medium' => 'required|numeric|min:0',
            'usia_high'   => 'required|numeric|min:0',

            'material_low'    => 'required|numeric|min:0',
            'material_medium' => 'required|numeric|min:0',
            'material_high'   => 'required|numeric|min:0',
        ];

        if ($user->role_id == 1) {
            $rules['organization_id'] = 'required|exists:organizations,organization_id';
        }

        $messages = [
            'required' => 'Kolom ini wajib diisi.',
            'numeric'  => 'Harus berupa angka.',
            'exists'   => 'Organisasi tidak valid.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $option = Option::find(self::OPTION_EWS_CALCULATION_ID);
        $allData = $option ? $option->text_value : [];

        $newOrgData = [
            'keparahan' => [
                'low'    => $request->keparahan_low,
                'medium' => $request->keparahan_medium,
                'high'   => $request->keparahan_high,
            ],
            'geografis' => [
                'low'    => $request->geo_low,
                'medium' => $request->geo_medium,
                'high'   => $request->geo_high,
            ],
            'usia' => [
                'low'    => $request->usia_low,
                'medium' => $request->usia_medium,
                'high'   => $request->usia_high,
            ],
            'material' => [
                'low'    => $request->material_low,
                'medium' => $request->material_medium,
                'high'   => $request->material_high,
            ]
        ];

        $allData[$orgId] = $newOrgData;

        if (!$option) {
            $option = new Option();
            $option->id = self::OPTION_EWS_CALCULATION_ID;
        }

        $option->text_value = $allData;
        $option->save();

        return redirect()->back()
            ->with('success', 'Pengaturan EWS berhasil disimpan.')
            ->withInput(['organization_id' => $orgId]);
    }
}
