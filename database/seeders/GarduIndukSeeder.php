<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GarduInduk;
use App\Models\Organization;
use Carbon\Carbon;

class GarduIndukSeeder extends Seeder
{
    public function run()
    {
        if (Organization::count() == 0) {
            Organization::factory()->create();
        }
        $orgId = Organization::first()->organization_id;

        GarduInduk::create([
            'organization_id' => $orgId,
            'gardu_induk'     => 'GI Cawang Baru',
            'keterangan'      => 'Data inspeksi komprehensif dengan 3 bay utama.',
            'foto'            => null,
            'riwayat'         => [
                [
                    'name' => 'Trafo 1',
                    'anomalies' => [
                        [
                            "use_ews"          => "1",
                            "jenis_anomali"    => "Hotspot Klem AA",
                            "jumlah_titik"     => 3,
                            "fasa"             => ["R", "S"],
                            "busbar"           => "Busbar A",
                            "tanggal_inspeksi" => Carbon::now()->subDays(5)->format('Y-m-d'),
                            "status_pekerjaan" => "Belum Dikerjakan",
                            "keparahan"        => "High",
                            "geografis"        => "High",
                            "usia"             => "Medium",
                            "material"         => "High",
                            "tanggal_ews"      => Carbon::now()->addDays(2)->format('Y-m-d'),
                            "foto"             => null
                        ],
                        [
                            "use_ews"          => "1",
                            "jenis_anomali"    => "Isolator Kotor",
                            "jumlah_titik"     => 12,
                            "fasa"             => ["T"],
                            "busbar"           => "Busbar A",
                            "tanggal_inspeksi" => Carbon::now()->subDays(10)->format('Y-m-d'),
                            "status_pekerjaan" => "Sudah Dikerjakan",
                            "keparahan"        => "Low",
                            "geografis"        => "Low",
                            "usia"             => "Low",
                            "material"         => "Low",
                            "tanggal_ews"      => Carbon::now()->subDays(9)->format('Y-m-d'),
                            "foto"             => null
                        ],
                        [
                            "use_ews"          => "0",
                            "jenis_anomali"    => "Minyak Rembes Bushing",
                            "jumlah_titik"     => 1,
                            "fasa"             => ["R"],
                            "busbar"           => "Body Trafo",
                            "tanggal_inspeksi" => Carbon::now()->subDays(1)->format('Y-m-d'),
                            "status_pekerjaan" => "Belum Dikerjakan",
                            "keparahan"        => "Medium",
                            "geografis"        => null,
                            "usia"             => null,
                            "material"         => null,
                            "tanggal_ews"      => null,
                            "foto"             => null
                        ]
                    ]
                ],
                [
                    'name' => 'Line Depok 1',
                    'anomalies' => [
                        [
                            "use_ews"          => "1",
                            "jenis_anomali"    => "Korona Ring Berkarat",
                            "jumlah_titik"     => 2,
                            "fasa"             => ["S", "T"],
                            "busbar"           => "Line Side",
                            "tanggal_inspeksi" => Carbon::now()->subMonths(1)->format('Y-m-d'),
                            "status_pekerjaan" => "Belum Dikerjakan",
                            "keparahan"        => "Medium",
                            "geografis"        => "High",
                            "usia"             => "High",
                            "material"         => "Medium",
                            "tanggal_ews"      => Carbon::now()->addWeeks(2)->format('Y-m-d'),
                            "foto"             => null
                        ],
                        [
                            "use_ews"          => "1",
                            "jenis_anomali"    => "Flashover Isolator",
                            "jumlah_titik"     => 1,
                            "fasa"             => ["R"],
                            "busbar"           => "Support 2",
                            "tanggal_inspeksi" => Carbon::now()->subDays(20)->format('Y-m-d'),
                            "status_pekerjaan" => "Sudah Dikerjakan",
                            "keparahan"        => "High",
                            "geografis"        => "High",
                            "usia"             => "Medium",
                            "material"         => "High",
                            "tanggal_ews"      => Carbon::now()->subDays(15)->format('Y-m-d'),
                            "foto"             => null
                        ]
                    ]
                ],
                [
                    'name' => 'Kopel Busbar 150kV',
                    'anomalies' => [
                        [
                            "use_ews"          => "1",
                            "jenis_anomali"    => "Baut Klem Kendor",
                            "jumlah_titik"     => 5,
                            "fasa"             => ["R", "S", "T"],
                            "busbar"           => "Busbar B",
                            "tanggal_inspeksi" => Carbon::now()->subDays(3)->format('Y-m-d'),
                            "status_pekerjaan" => "Belum Dikerjakan",
                            "keparahan"        => "High",
                            "geografis"        => "Low",
                            "usia"             => "High",
                            "material"         => "Medium",
                            "tanggal_ews"      => Carbon::now()->addDays(5)->format('Y-m-d'),
                            "foto"             => null
                        ],
                        [
                            "use_ews"          => "0",
                            "jenis_anomali"    => "Lampu Indikator Mati",
                            "jumlah_titik"     => 2,
                            "fasa"             => ["-"],
                            "busbar"           => "Panel Kontrol",
                            "tanggal_inspeksi" => Carbon::now()->subDays(30)->format('Y-m-d'),
                            "status_pekerjaan" => "Belum Dikerjakan",
                            "keparahan"        => "Low",
                            "geografis"        => null,
                            "usia"             => null,
                            "material"         => null,
                            "tanggal_ews"      => null,
                            "foto"             => null
                        ]
                    ]
                ]
            ],
        ]);
    }
}