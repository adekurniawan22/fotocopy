<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkPlan;
use App\Models\Organization;
use Carbon\Carbon;

class WorkPlanSeeder extends Seeder
{
    public function run()
    {
        $orgId = Organization::first()->organization_id;

        $data = [

            // ============================
            // TAHUN 2024
            // ============================
            [
                'name' => 'Audit Internal Q1 2024',
                'start' => [2024, 1, 10],
                'finish' => [2024, 1, 15],
                'items' => [
                    ['name' => 'Persiapan Dokumen', 'checked' => true],
                    ['name' => 'Wawancara Staff', 'checked' => true],
                    ['name' => 'Laporan Akhir', 'checked' => true],
                ],
                'done' => true,
            ],
            [
                'name' => 'Gathering Tahunan 2024',
                'start' => [2024, 5, 20],
                'finish' => [2024, 5, 21],
                'items' => [
                    ['name' => 'Booking Venue', 'checked' => true],
                    ['name' => 'Undangan Peserta', 'checked' => true],
                ],
                'done' => true,
            ],
            [
                'name' => 'Evaluasi Akhir Tahun 2024',
                'start' => [2024, 12, 10],
                'finish' => [2024, 12, 20],
                'items' => [
                    ['name' => 'Kumpul Data Kinerja', 'checked' => true],
                    ['name' => 'Presentasi Direksi', 'checked' => false],
                ],
                'done' => false,
            ],

            // ============================
            // TAHUN 2025
            // ============================
            [
                'name' => 'Rapat Kickoff 2025',
                'start' => [2025, 1, 5],
                'finish' => [2025, 1, 5],
                'items' => [
                    ['name' => 'Siapkan Materi', 'checked' => true],
                    ['name' => 'Booking Ruang Rapat', 'checked' => true],
                ],
                'done' => true,
            ],
            [
                'name' => 'Pengadaan Laptop Baru',
                'start' => [2025, 2, 1],
                'finish' => [2025, 2, 10],
                'items' => [
                    ['name' => 'Survey Vendor', 'checked' => true],
                    ['name' => 'Approval Budget', 'checked' => true],
                    ['name' => 'Purchase Order', 'checked' => false],
                ],
                'done' => false,
            ],
            [
                'name' => 'Pelatihan K3 Karyawan',
                'start' => [2025, 3, 15],
                'finish' => [2025, 3, 16],
                'items' => [
                    ['name' => 'Kontak Pemateri', 'checked' => true],
                    ['name' => 'Edaran Peserta', 'checked' => false],
                ],
                'done' => false,
            ],
            [
                'name' => 'Maintenance Server Q2',
                'start' => [2025, 4, 10],
                'finish' => [2025, 4, 12],
                'items' => [
                    ['name' => 'Backup Database', 'checked' => false],
                    ['name' => 'Update OS', 'checked' => false],
                ],
                'done' => false,
            ],
            [
                'name' => 'Audit Eksternal ISO',
                'start' => [2025, 5, 20],
                'finish' => [2025, 5, 25],
                'items' => [
                    ['name' => 'Review SOP', 'checked' => true],
                    ['name' => 'Pendampingan Auditor', 'checked' => true],
                    ['name' => 'Closing Meeting', 'checked' => true],
                ],
                'done' => true,
            ],
            [
                'name' => 'Perayaan HUT RI',
                'start' => [2025, 8, 17],
                'finish' => [2025, 8, 17],
                'items' => [
                    ['name' => 'Pembentukan Panitia', 'checked' => true],
                    ['name' => 'Pelaksanaan Lomba', 'checked' => false],
                ],
                'done' => false,
            ],
            [
                'name' => 'Laporan Tahunan 2025',
                'start' => [2025, 12, 20],
                'finish' => [2025, 12, 30],
                'items' => [
                    ['name' => 'Kompilasi Data', 'checked' => false],
                    ['name' => 'Review Draft', 'checked' => false],
                    ['name' => 'Cetak Laporan', 'checked' => false],
                ],
                'done' => false,
            ],
        ];

        // Loop create
        foreach ($data as $item) {
            WorkPlan::create([
                'organization_id' => $orgId,
                'work_plan_name'  => $item['name'],
                'date_start'      => Carbon::create(...$item['start']),
                'date_finish'     => Carbon::create(...$item['finish']),
                'list_items'      => $item['items'],
                'is_done'         => $item['done'],
            ]);
        }
    }
}
