<?php

namespace App\Exports;

use App\Models\Partnership;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PartnershipExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $keyword;

    public function __construct($keyword)
    {
        $this->keyword = $keyword;
    }

    public function query()
    {
        $query = Partnership::with(['organization']);

        if ($this->keyword) {
            $keyword = $this->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('partnership_name', 'like', '%' . $keyword . '%')
                    ->orWhere('penanggung_jawab', 'like', '%' . $keyword . '%')
                    ->orWhere('alamat', 'like', '%' . $keyword . '%')
                    ->orWhere('no_hp', 'like', '%' . $keyword . '%')
                    ->orWhereHas('organization', function ($orgQuery) use ($keyword) {
                        $orgQuery->where('organization_name', 'like', '%' . $keyword . '%');
                    });
            });
        }

        return $query->orderBy('partnership_name', 'asc');
    }

    public function map($partnership): array
    {
        return [
            $partnership->partnership_name,
            $partnership->penanggung_jawab,
            optional($partnership->organization)->organization_name ?? '-',
            $partnership->no_hp ?? '-',
            $partnership->alamat ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Partnership',
            'Penanggung Jawab',
            'Organisasi',
            'No. HP',
            'Alamat',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
