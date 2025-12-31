<?php

namespace App\Exports;

use App\Models\WorkPlan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WorkPlanExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $year;
    protected $roleId;
    protected $organizationId;

    public function __construct(int $year, int $roleId, $organizationId = null)
    {
        $this->year = $year;
        $this->roleId = $roleId;
        $this->organizationId = $organizationId;
    }

    public function query()
    {
        $query = WorkPlan::query()
            ->with('organization')
            ->whereYear('date_start', $this->year);

        if ($this->roleId != 1) {
            $query->where('organization_id', $this->organizationId);
        }

        return $query->orderBy('date_start', 'asc');
    }

    public function map($workPlan): array
    {
        $rawItems = $workPlan->list_items;
        $listItems = [];

        if (is_array($rawItems)) {
            $listItems = $rawItems;
        } elseif (is_string($rawItems)) {
            $listItems = json_decode($rawItems, true);
        }

        if (!is_array($listItems)) $listItems = [];

        $totalItems = count($listItems);
        $doneItems = 0;

        foreach ($listItems as $item) {
            if (isset($item['checked']) && ($item['checked'] === true || $item['checked'] === 'true' || $item['checked'] == 1)) {
                $doneItems++;
            }
        }

        $progressString = $totalItems > 0 ? "$doneItems / $totalItems" : "0";
        $statusString = ($workPlan->is_done) ? 'Selesai' : 'Belum Selesai';
        $organizationName = $workPlan->organization->organization_name ?? '-';

        return [
            $workPlan->work_plan_name,
            $organizationName,
            $workPlan->date_start,
            $workPlan->date_finish,
            $statusString,
            $progressString
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Rencana Kerja',
            'Organisasi',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Status',
            'Progress Tugas',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
