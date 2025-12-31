<?php

namespace App\Exports;

use App\Models\Tool;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ToolExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $user;
    protected $keyword;
    protected $warehouseId;

    public function __construct($user, $keyword, $warehouseId)
    {
        $this->user = $user;
        $this->keyword = $keyword;
        $this->warehouseId = $warehouseId;
    }

    public function query()
    {
        $query = Tool::with(['organization', 'warehouse'])
            ->whereHas('warehouse', function ($q) {
                $q->where('is_active', 1);
            });

        // 1. Filter Role (Sama seperti Controller)
        if ($this->user->role_id != 1) {
            $query->where('organization_id', $this->user->organization_id);
        }

        // 2. Filter Gudang
        if ($this->warehouseId) {
            $query->where('warehouse_id', $this->warehouseId);
        }

        // 3. Filter Keyword
        if ($this->keyword) {
            $keyword = $this->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', '%' . $keyword . '%')
                    ->orWhere('jenis', 'like', '%' . $keyword . '%')
                    ->orWhere('merk', 'like', '%' . $keyword . '%');
            });
        }

        return $query->orderBy('nama', 'asc');
    }

    public function map($tool): array
    {
        $data = [
            $tool->nama,
            $tool->merk ?? '-',
        ];

        // Jika Admin, tambahkan kolom Organisasi
        if ($this->user->role_id == 1) {
            $data[] = $tool->organization->organization_name ?? '-';
        }

        $data[] = $tool->warehouse->warehouse_name ?? '-';
        $data[] = $tool->jenis;
        $data[] = $tool->jumlah . ' ' . $tool->satuan;

        // Format tanggal menjadi Y-m-d
        $data[] = $tool->tanggal_pengadaan
            ? \Carbon\Carbon::parse($tool->tanggal_pengadaan)->format('Y-m-d')
            : '-';

        $data[] = $tool->tanggal_kadaluarsa
            ? \Carbon\Carbon::parse($tool->tanggal_kadaluarsa)->format('Y-m-d')
            : '-';

        return $data;
    }


    public function headings(): array
    {
        $headers = [
            'Nama Alat',
            'Merk',
        ];

        if ($this->user->role_id == 1) {
            $headers[] = 'Organisasi';
        }

        $headers = array_merge($headers, [
            'Gudang',
            'Jenis',
            'Jumlah',
            'Tgl Pengadaan',
            'Tgl Kadaluarsa',
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
