<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jaringan;
use App\Models\Organization;
use Carbon\Carbon;

class JaringanSeeder extends Seeder
{
    public function run()
    {
        // Pastikan ada Organization
        if (Organization::count() == 0) {
            Organization::factory()->create();
        }
        $orgId = Organization::first()->organization_id;

        Jaringan::create([
            'organization_id' => $orgId,
            'bay_line'        => 'SUTT 150kV Cawang - Bekasi',
            'keterangan'      => 'Inspeksi korosi dan ruang bebas (ROW) jalur utama.',
            'foto'            => null,
            'riwayat'         => [
                // TOWER 1: Fokus pada komponen isolator dan EWS
                [
                    'no_tower'    => 'Tower 1',
                    'jenis_tower' => 'Double Tension - Double Tension',
                    'anomalies'   => [
                        [
                            "use_ews"          => "1",
                            "jenis_anomali"    => "Sackle Isolator Korosi",
                            "jumlah_titik"     => 2,
                            "fasa"             => ["T", "GSW"],
                            "busbar"           => "Area Konduktor",
                            "tanggal_inspeksi" => Carbon::now()->subDays(10)->format('Y-m-d'),
                            "status_pekerjaan" => "Belum Dikerjakan",
                            "keparahan"        => "Medium",
                            "geografis"        => "High",
                            "usia"             => "High",
                            "material"         => "Low",
                            "tanggal_ews"      => Carbon::now()->addWeeks(3)->format('Y-m-d'),
                            "foto"             => null
                        ],
                        [
                            "use_ews"          => "1",
                            "jenis_anomali"    => "Isolator Pecah (Flashover)",
                            "jumlah_titik"     => 1,
                            "fasa"             => ["R"],
                            "busbar"           => "Top Cross Arm",
                            "tanggal_inspeksi" => Carbon::now()->subDays(2)->format('Y-m-d'),
                            "status_pekerjaan" => "Belum Dikerjakan",
                            "keparahan"        => "High",
                            "geografis"        => "Low",
                            "usia"             => "Medium",
                            "material"         => "High",
                            "tanggal_ews"      => Carbon::now()->addDays(2)->format('Y-m-d'),
                            "foto"             => null
                        ]
                    ]
                ],
                // TOWER 2: Fokus pada penjepit konduktor dan data Non-EWS
                [
                    'no_tower'    => 'Tower 2',
                    'jenis_tower' => 'Single Suspension',
                    'anomalies'   => [
                        [
                            "use_ews"          => "0",
                            "jenis_anomali"    => "Suspension Clamp Korosi",
                            "jumlah_titik"     => 4,
                            "fasa"             => ["R", "S"],
                            "busbar"           => "Body Tower",
                            "tanggal_inspeksi" => Carbon::now()->subMonths(1)->format('Y-m-d'),
                            "status_pekerjaan" => "Sudah Dikerjakan",
                            "keparahan"        => "Low",
                            "geografis"        => null,
                            "usia"             => null,
                            "material"         => null,
                            "tanggal_ews"      => null,
                            "foto"             => null
                        ],
                        [
                            "use_ews"          => "1",
                            "jenis_anomali"    => "Hotspot Joint Konduktor",
                            "jumlah_titik"     => 1,
                            "fasa"             => ["S"],
                            "busbar"           => "Span Tower 2-3",
                            "tanggal_inspeksi" => Carbon::now()->subDays(5)->format('Y-m-d'),
                            "status_pekerjaan" => "Belum Dikerjakan",
                            "keparahan"        => "High",
                            "geografis"        => "Medium",
                            "usia"             => "High",
                            "material"         => "High",
                            "tanggal_ews"      => Carbon::now()->addDays(1)->format('Y-m-d'),
                            "foto"             => null
                        ]
                    ]
                ],
                // TOWER 3: Fokus pada pondasi dan lingkungan (Data anomali banyak)
                [
                    'no_tower'    => 'Tower 3',
                    'jenis_tower' => 'Tension Tower',
                    'anomalies'   => [
                        [
                            "use_ews"          => "1",
                            "jenis_anomali"    => "Stub Pondasi Amblas",
                            "jumlah_titik"     => 1,
                            "fasa"             => ["-"],
                            "busbar"           => "Leg A",
                            "tanggal_inspeksi" => Carbon::now()->subDays(15)->format('Y-m-d'),
                            "status_pekerjaan" => "Belum Dikerjakan",
                            "keparahan"        => "High",
                            "geografis"        => "High",
                            "usia"             => "Low",
                            "material"         => "High",
                            "tanggal_ews"      => Carbon::now()->addWeeks(1)->format('Y-m-d'),
                            "foto"             => null
                        ],
                        [
                            "use_ews"          => "0",
                            "jenis_anomali"    => "Pohon Masuk Ruang Bebas",
                            "jumlah_titik"     => 10,
                            "fasa"             => ["R", "S", "T"],
                            "busbar"           => "Span 3-4",
                            "tanggal_inspeksi" => Carbon::now()->format('Y-m-d'),
                            "status_pekerjaan" => "Belum Dikerjakan",
                            "keparahan"        => "Medium",
                            "geografis"        => null,
                            "usia"             => null,
                            "material"         => null,
                            "tanggal_ews"      => null,
                            "foto"             => null
                        ],
                        [
                            "use_ews"          => "1",
                            "jenis_anomali"    => "Member Tower Hilang",
                            "jumlah_titik"     => 6,
                            "fasa"             => ["-"],
                            "busbar"           => "Section 2",
                            "tanggal_inspeksi" => Carbon::now()->subDays(20)->format('Y-m-d'),
                            "status_pekerjaan" => "Sudah Dikerjakan",
                            "keparahan"        => "Medium",
                            "geografis"        => "Low",
                            "usia"             => "High",
                            "material"         => "Medium",
                            "tanggal_ews"      => Carbon::now()->subDays(5)->format('Y-m-d'),
                            "foto"             => null
                        ]
                    ]
                ]
            ],
        ]);
    }
}