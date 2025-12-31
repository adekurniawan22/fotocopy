<?php

namespace App\Exports;

use App\Models\Warehouse;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WarehouseExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $user;
    protected $keyword;

    public function __construct($user, $keyword)
    {
        $this->user = $user;
        $this->keyword = $keyword;
    }

    public function query()
    {
        // Query dasar + hitung jumlah alat (tool_count)
        $query = Warehouse::with('organization')->withCount('tool');

        // 1. Filter Role (Jika bukan admin, hanya organisasi sendiri)
        if ($this->user->role_id != 1) {
            $query->where('organization_id', $this->user->organization_id);
        }

        // 2. Filter Keyword
        if ($this->keyword) {
            $keyword = $this->keyword;
            $query->where('warehouse_name', 'like', '%' . $keyword . '%');
        }

        return $query->orderBy('warehouse_name', 'asc');
    }

    public function map($warehouse): array
    {
        $data = [
            $warehouse->warehouse_name,
        ];

        // Jika Admin, tambahkan kolom Organisasi
        if ($this->user->role_id == 1) {
            $data[] = $warehouse->organization->organization_name ?? '-';
        }

        // Kolom Jumlah Alat (dari withCount 'tool')
        $data[] = $warehouse->tool_count;
        
        // Kolom Status
        $data[] = $warehouse->is_active ? 'Aktif' : 'Tidak Aktif';

        return $data;
    }

    public function headings(): array
    {
        $headers = [
            'Nama Gudang',
        ];

        if ($this->user->role_id == 1) {
            $headers[] = 'Organisasi';
        }

        $headers = array_merge($headers, [
            'Jumlah Alat',
            'Status',
        ]);

        return $headers;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Baris 1 Bold
        ];
    }
}