<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $keyword;
    protected $roleId;

    public function __construct($keyword, $roleId)
    {
        $this->keyword = $keyword;
        $this->roleId = $roleId;
    }

    public function query()
    {
        $query = User::with(['organization', 'role']);

        if ($this->keyword) {
            $keyword = $this->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('nip', 'like', '%' . $keyword . '%')
                    ->orWhere('no_hp', 'like', '%' . $keyword . '%')
                    ->orWhereHas('organization', function ($orgQuery) use ($keyword) {
                        $orgQuery->where('organization_name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhereHas('role', function ($roleQuery) use ($keyword) {
                        $roleQuery->where('role_name', 'like', '%' . $keyword . '%');
                    });
            });
        }

        if ($this->roleId) {
            $query->where('role_id', $this->roleId);
        }

        return $query->orderBy('name', 'asc');
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->nip ?? '-',
            optional($user->role)->role_name,
            optional($user->organization)->organization_name,
            $user->no_hp ?? '-',
            $user->is_active ? 'Aktif' : 'Nonaktif',
        ];
    }

    // Judul Header Excel
    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'NIP',
            'Role',
            'Organisasi',
            'No. HP',
            'Status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
