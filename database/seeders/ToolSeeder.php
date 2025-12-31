<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tool;
use App\Models\Warehouse;
use Faker\Factory as Faker;

class ToolSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $warehouses = Warehouse::all();

        $merkOptions = [
            'Kyoritsu', 'Fluke', 'Hastings', 'Ritz', 'NGK', 'Schneider', 
            'ABB', 'Krisbow', 'Tekiro', 'Bosch', 'Makita', '3M', 
            'Supreme', 'Kabelindo', 'Eiger', 'Petzl'
        ];

        $toolsByCategory = [
            'Peralatan K3 & APD' => [
                'Helm Safety Proyek (Merah/Kuning)',
                'Full Body Harness Double Lanyard',
                'Sarung Tangan Tahan Tegangan 20kV',
                'Sepatu Safety Tahan Listrik 20kV',
                'Stick 20kV (Telescopic Hot Stick)',
                'Grounding Set Portable 20kV'
            ],
            'Alat Ukur (Instrumentation)' => [
                'Insulation Tester (Megger) 5kV',
                'Earth Tester Digital',
                'Tang Ampere (Clamp Meter) Digital',
                'Phase Detector 20kV',
                'Thermal Imager (Kamera Thermovision)',
                'Power Quality Analyzer'
            ],
            'Peralatan Kerja Distribusi' => [
                'Tang Press Hidrolik (Hydraulic Crimping)',
                'Tackle / Chain Block 3 Ton',
                'Puller (Lever Block) 1.5 Ton',
                'Webbing Sling 5 Ton',
                'Tangga Fiber Extension',
                'Mesin Potong Rumput (Gendong)'
            ],
            'Material Distribusi (MDU)' => [
                'Fuse Cut Out (FCO) 24kV',
                'Lightning Arrester 20kV',
                'Isolator Tumpu (Pin Post) 20kV',
                'Isolator Tarik (Hang) Polymer 20kV',
                'Joint Sleeve Al-Al 70mm',
                'Connector CCO (Compression Connector)'
            ],
            'Kabel & Konduktor' => [
                'Kabel TIC 3x70+50+16 mm2',
                'Kabel NYY 4x16 mm2',
                'Kabel A3CS 150 mm2',
                'Kabel Tanah (SKTM) 20kV NA2XSY',
                'Kabel BC (Bare Copper) 50mm'
            ]
        ];

        $usedToolsPerOrganization = [];

        foreach ($warehouses as $warehouse) {
            $orgId = $warehouse->organization_id;

            if (!isset($usedToolsPerOrganization[$orgId])) {
                $usedToolsPerOrganization[$orgId] = [];
            }

            $targetCount = rand(3, 5);
            $warehouseTools = [];

            $allPossibleTools = [];
            foreach ($toolsByCategory as $category => $tools) {
                foreach ($tools as $toolName) {
                    $allPossibleTools[] = ['jenis' => $category, 'nama' => $toolName];
                }
            }

            shuffle($allPossibleTools);

            foreach ($allPossibleTools as $toolCandidate) {
                if (count($warehouseTools) >= $targetCount) break;

                if (!in_array($toolCandidate['nama'], $usedToolsPerOrganization[$orgId])) {
                    
                    $warehouseTools[] = $toolCandidate;
                    $usedToolsPerOrganization[$orgId][] = $toolCandidate['nama'];
                }
            }

            foreach ($warehouseTools as $toolData) {
                $tglPengadaan = $faker->dateTimeBetween('-3 years', '-1 month');
                
                $hasExpiry = in_array($toolData['jenis'], ['Peralatan K3 & APD', 'Material Distribusi (MDU)']);
                $tglKadaluarsa = $hasExpiry ? $faker->dateTimeBetween('now', '+5 years') : null;

                Tool::create([
                    'organization_id'     => $warehouse->organization_id,
                    'warehouse_id'        => $warehouse->warehouse_id,
                    'jenis'               => $toolData['jenis'],
                    'nama'                => $toolData['nama'],
                    'merk'                => $faker->randomElement($merkOptions),
                    'deskripsi'           => $this->getToolDescription($toolData['nama']),
                    'jumlah'              => $this->getJumlahLogis($toolData['nama']),
                    'satuan'              => $this->getSatuanForTool($toolData['nama']),
                    'tanggal_pengadaan'   => $tglPengadaan,
                    'tanggal_kadaluarsa'  => $tglKadaluarsa,
                ]);
            }
        }
    }

    private function getToolDescription($toolName): string
    {
        $map = [
            'Stick 20kV'      => 'Tongkat isolasi teleskopik untuk manuver Jaringan Tegangan Menengah',
            'Megger'          => 'Alat ukur tahanan isolasi kabel/trafo',
            'Tang Press'      => 'Alat crimping sepatu kabel (skun) tipe hidrolik',
            'FCO'             => 'Pengaman lebur jaringan distribusi tegangan menengah',
            'Arrester'        => 'Pengaman jaringan dari surja petir',
            'Thermovision'    => 'Kamera pendeteksi titik panas (hotspot) pada sambungan listrik',
            'Grounding Set'   => 'Peralatan pembumian sementara untuk keamanan pekerjaan',
            'Kabel TIC'       => 'Twisted Insulated Cable untuk Jaringan Tegangan Rendah',
            'Chain Block'     => 'Alat angkat beban manual (katrol rantai)',
        ];

        foreach ($map as $key => $desc) {
            if (stripos($toolName, $key) !== false) {
                return $desc;
            }
        }

        return 'Peralatan standar operasional distribusi tenaga listrik';
    }

    private function getSatuanForTool(string $toolName): string
    {
        if (stripos($toolName, 'Kabel') !== false) return 'Meter';
        if (stripos($toolName, 'Sarung Tangan') !== false) return 'Pasang';
        if (stripos($toolName, 'Sepatu') !== false) return 'Pasang';
        if (stripos($toolName, 'Set') !== false) return 'Set';
        if (stripos($toolName, 'FCO') !== false || stripos($toolName, 'Arrester') !== false || stripos($toolName, 'Isolator') !== false) return 'Buah';
        if (stripos($toolName, 'Sleeve') !== false || stripos($toolName, 'Connector') !== false) return 'Pcs';
        
        return 'Unit';
    }

    private function getJumlahLogis(string $toolName): int
    {
        if (stripos($toolName, 'Kabel') !== false) return rand(100, 1000);
        if (stripos($toolName, 'Sleeve') !== false || stripos($toolName, 'Connector') !== false) return rand(50, 200);
        if (stripos($toolName, 'FCO') !== false || stripos($toolName, 'Isolator') !== false) return rand(10, 50);
        
        return rand(1, 5);
    }
}