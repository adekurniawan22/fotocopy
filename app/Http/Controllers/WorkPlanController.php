<?php

namespace App\Http\Controllers;

use App\Exports\WorkPlanExport;
use App\Models\Organization;
use App\Models\WorkPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class WorkPlanController extends Controller
{
    public function index(Request $request)
    {
        $isAdmin = Auth::user()->role_id == 1;

        if ($request->ajax()) {
            $query = WorkPlan::with('organization')
                ->where('date_start', '<=', $request->end)
                ->where('date_finish', '>=', $request->start);

            if (!$isAdmin) {
                $query->where('organization_id', Auth::user()->organization_id);
            }

            $workPlans = $query->get();

            $events = $workPlans->map(function ($plan) use ($isAdmin) {
                return [
                    'id' => $plan->work_plan_id,
                    'title' => $plan->work_plan_name,
                    'start' => $plan->date_start->format('Y-m-d'),
                    'end' => $plan->date_finish->format('Y-m-d'),
                    'allDay' => true,
                    'extendedProps' => [
                        'organization_id' => $plan->organization_id,
                        'organization_name' => $isAdmin ? ($plan->organization->organization_name ?? 'N/A') : null,
                        'list_items' => $plan->list_items,
                        'is_done' => $plan->is_done,
                    ],
                    'className' => $plan->is_done ? 'fc-event-done' : ''
                ];
            });

            return response()->json($events);
        }

        $organizations = $isAdmin ? Organization::orderBy('organization_name', 'asc')->get() : collect();

        return view('work-plan.list', compact('organizations', 'isAdmin'));
    }

    public function store(Request $request)
    {
        $isAdmin = Auth::user()->role_id == 1;

        $request->merge([
            'list_items' => json_decode($request->list_items, true)
        ]);

        $rules = [
            'work_plan_name' => 'required|string|max:255',
            'date_start' => 'required|date',
            'date_finish' => 'required|date|after_or_equal:date_start',
            'list_items' => 'required|array|min:1',
            'list_items.*.name' => 'required|string',
        ];

        if ($isAdmin) {
            $rules['organization_id'] = 'required|exists:organizations,organization_id';
        }

        $messages = [
            'work_plan_name.required' => 'Nama rencana kerja wajib diisi.',
            'work_plan_name.max' => 'Nama rencana kerja maksimal 255 karakter.',
            'date_start.required' => 'Tanggal mulai wajib diisi.',
            'date_start.date' => 'Format tanggal mulai tidak valid.',
            'date_finish.required' => 'Tanggal selesai wajib diisi.',
            'date_finish.date' => 'Format tanggal selesai tidak valid.',
            'date_finish.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'organization_id.required' => 'Organisasi wajib dipilih.',
            'organization_id.exists' => 'Organisasi tidak ditemukan dalam database.',
            'list_items.required' => 'Daftar tugas (checklist) wajib diisi.',
            'list_items.min' => 'Minimal harus ada 1 tugas dalam daftar.',
            'list_items.*.name.required' => 'Nama tugas tidak boleh kosong.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except(['list_items']);

        $listItems = $request->list_items;

        $data['is_done'] = $this->calculateIsDone($listItems);
        $data['list_items'] = $listItems;

        if (!$isAdmin) {
            $data['organization_id'] = Auth::user()->organization_id;
        }

        WorkPlan::create($data);

        return response()->json(['success' => 'Rencana kerja berhasil ditambahkan.']);
    }

    public function update(Request $request, WorkPlan $workPlan)
    {
        $isAdmin = Auth::user()->role_id == 1;

        if ($request->has('list_items_json_string')) {
            $request->merge([
                'list_items' => json_decode($request->list_items_json_string, true)
            ]);

            $rules = [
                'work_plan_name' => 'required|string|max:255',
                'date_start' => 'required|date',
                'date_finish' => 'required|date|after_or_equal:date_start',
                'list_items' => 'required|array|min:1',
                'list_items.*.name' => 'required|string',
            ];

            if ($isAdmin) {
                $rules['organization_id'] = 'required|exists:organizations,organization_id';
            }

            $messages = [
                'work_plan_name.required' => 'Nama rencana kerja wajib diisi.',
                'work_plan_name.max' => 'Nama rencana kerja maksimal 255 karakter.',
                'date_start.required' => 'Tanggal mulai wajib diisi.',
                'date_start.date' => 'Format tanggal mulai tidak valid.',
                'date_finish.required' => 'Tanggal selesai wajib diisi.',
                'date_finish.date' => 'Format tanggal selesai tidak valid.',
                'date_finish.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
                'organization_id.required' => 'Organisasi wajib dipilih.',
                'organization_id.exists' => 'Organisasi tidak valid.',
                'list_items.required' => 'Daftar tugas (checklist) wajib diisi.',
                'list_items.min' => 'Minimal harus ada 1 tugas dalam daftar.',
                'list_items.*.name.required' => 'Nama tugas tidak boleh kosong.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $request->except(['list_items', 'list_items_json_string']);
            $listItems = $request->list_items;

            $data['is_done'] = $this->calculateIsDone($listItems);
            $data['list_items'] = $listItems;
        } else {
            $data = $request->only(['date_start', 'date_finish', 'work_plan_name', 'organization_id']);
        }

        if (!$isAdmin && isset($data['organization_id'])) {
            $data['organization_id'] = Auth::user()->organization_id;
        }

        $workPlan->update($data);

        return response()->json(['success' => 'Rencana kerja berhasil diperbarui.']);
    }

    public function destroy(WorkPlan $workPlan)
    {
        try {
            $workPlan->delete();
            return response()->json([
                'success' => 'Rencana kerja berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal menghapus data.'
            ], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        $year = $request->input('year', date('Y'));

        $roleId = session('user_data.role_id');
        $organizationId = session('user_data.organization_id');

        $waktuIndonesia = now()->setTimezone('Asia/Jakarta')->format('Y-m-d H.i');

        $fileName = "Rencana_Kerja_{$year}_{$waktuIndonesia}.xlsx";

        return Excel::download(new WorkPlanExport($year, $roleId, $organizationId), $fileName);
    }


    public function listByYear(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|digits:4'
        ]);

        $year = $request->year;
        $isAdmin = Auth::user()->role_id == 1;

        $query = WorkPlan::whereYear('date_start', $year);

        if (!$isAdmin) {
            $query->where('organization_id', Auth::user()->organization_id);
        }

        $workPlans = $query->orderBy('date_start', 'asc')
            ->get()
            ->groupBy('is_done');

        $result = [
            '0' => $workPlans->get(0, collect()),
            '1' => $workPlans->get(1, collect())
        ];

        return response()->json($result);
    }

    private function calculateIsDone($listItems)
    {
        if (is_string($listItems)) {
            $listItems = json_decode($listItems, true);
        }

        if (empty($listItems) || !is_array($listItems)) {
            return false;
        }

        foreach ($listItems as $item) {
            if (empty($item['checked']) || $item['checked'] === 'false' || $item['checked'] === false || $item['checked'] === 0) {
                return false;
            }
        }

        return true;
    }
}
