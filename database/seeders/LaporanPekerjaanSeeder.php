<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class LaporanPekerjaanSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $userIds = DB::table('users')->pluck('user_id')->toArray();
        $orgIds = DB::table('organizations')->pluck('organization_id')->toArray();

        if (empty($userIds) || empty($orgIds)) {
            $this->command->info('User atau Organization kosong. Seeder dilewati.');
            return;
        }

        $plnReports = [
            [
                'judul' => 'Pemeliharaan Preventif Kubikel 20kV Gardu Distribusi GD-052',
                'lingkup' => "1. Pembersihan body kubikel dan busbar dari debu.\n2. Pengencangan baut konektor bushing.\n3. Pengecekan heater dan thermostat.\n4. Pengukuran tahanan isolasi.",
                'hasil' => 'Kondisi kubikel bersih, heater berfungsi normal, tidak ada baut kendor (sudah ditandai marking), nilai tahanan isolasi aman (> 1000 MΩ).',
                'lampiran_judul' => 'Foto Maintenance Kubikel'
            ],
            [
                'judul' => 'Inspeksi & Perampalan Pohon (ROW) Penyulang Merpati',
                'lingkup' => "1. Pemangkasan dahan pohon yang menyentuh JTM (Jarak Aman 3 meter).\n2. Inspeksi visual tiang miring di section 2.\n3. Pengecekan arrester yang terindikasi flashover.",
                'hasil' => 'ROW aman (jarak bebas sudah 3 meter), sampah dahan sudah diamankan warga, tiang miring perlu penanganan lanjut tim konstruksi.',
                'lampiran_judul' => 'Dokumentasi ROW Penyulang'
            ]
        ];

        $dataToInsert = [];

        foreach ($plnReports as $report) {
            
            $startDate = Carbon::now()->subDays(rand(1, 5));
            $endDate = (clone $startDate)->addHours(rand(3, 8));

            $lampiranList = [
                [
                    'judul_lampiran' => $report['lampiran_judul'],
                    'foto_sebelum'   => 'laporan/dummy/before.jpg',
                    'foto_proses'    => 'laporan/dummy/process.jpg',
                    'foto_sesudah'   => 'laporan/dummy/after.jpg'
                ]
            ];

            $dataToInsert[] = [
                'organization_id'     => $orgIds[array_rand($orgIds)],
                'judul_laporan'       => $report['judul'],
                'dasar_pelaksanaan'   => 'SPK/2025/TEK-DIST/' . strtoupper($faker->bothify('???####')), 
                'mulai_pelaksanaan'   => $startDate->format('Y-m-d'),
                'selesai_pelaksanaan' => $endDate->format('Y-m-d'),
                'lingkup_pekerjaan'   => $report['lingkup'],
                'hasil_pekerjaan'     => $report['hasil'],
                'lampiran'            => json_encode($lampiranList),
                'created_by'          => $userIds[array_rand($userIds)],
                
                'approved_by'         => null,
                'approved_at'         => null,
                'status'              => 'DRAFT',
                'revision_note'       => null,
                
                'created_at'          => Carbon::now()->subMinutes(rand(10, 60)),
                'updated_at'          => Carbon::now(),
            ];
        }

        DB::table('laporan_pekerjaan')->insert($dataToInsert);
    }
}