<?php

namespace Database\Seeders;

use App\Models\MethodTool;
use App\Models\Tool;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class MethodToolSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = Organization::all();

        foreach ($organizations as $org) {
            $orgTools = Tool::where('organization_id', $org->organization_id)
                ->where('jumlah', '>', 0)
                ->get();

            if ($orgTools->isEmpty()) {
                $this->command->info("Skip Organization ID {$org->organization_id}: Tidak ada alat tersedia.");
                continue;
            }

            $toolChunks = $orgTools->shuffle()->split(3);

            $methodNames = [
                'Metode Maintenance Rutin',
                'Metode Instalasi Jaringan Baru',
                'Metode Perbaikan Darurat'
            ];

            foreach ($methodNames as $index => $name) {
                if (!isset($toolChunks[$index]) || $toolChunks[$index]->isEmpty()) {
                    continue;
                }

                $toolsInThisMethod = $toolChunks[$index];
                $formattedTools = [];

                foreach ($toolsInThisMethod as $tool) {
                    $maxQty = $tool->jumlah > 5 ? 5 : $tool->jumlah;

                    $formattedTools[] = [
                        'id'     => $tool->tool_id,
                        'nama'   => $tool->nama,
                        'satuan' => $tool->satuan ?? 'Pcs',
                        'qty'    => rand(1, $maxQty)
                    ];
                }

                if (!empty($formattedTools)) {
                    MethodTool::create([
                        'organization_id' => $org->organization_id,
                        'nama_method'     => $name,
                        'list_tools'      => $formattedTools,
                    ]);
                }
            }
        }
    }
}
