<?php

namespace Database\Seeders;

use App\Models\HistoryTool;
use App\Models\Organization;
use App\Models\Tool;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class HistoryToolSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $org = Organization::first();
        $user = User::first();

        $tools = Tool::where('jumlah', '>', 0)->get();

        if (!$org || !$user || $tools->isEmpty()) {
            $this->command->info('Data Organization, User, atau Tool tidak lengkap. Seeder dibatalkan.');
            return;
        }

        $toolsForDraft = $tools->random(min(2, $tools->count()));
        $listToolsDraft = [];

        foreach ($toolsForDraft as $t) {
            $maxQty = min($t->jumlah, 5);

            $listToolsDraft[] = [
                'id'     => $t->tool_id,
                'nama'   => $t->nama,
                'satuan' => $t->satuan ?? 'Pcs',
                'qty'    => rand(1, $maxQty)
            ];
        }

        HistoryTool::create([
            'organization_id' => $org->organization_id,
            'type'            => 'Internal',
            'status'          => 'draft',
            'created_by'      => $user->user_id,
            'list_tools'      => [
                'method_name' => 'Persiapan Pasang Baru (PB) Cluster A',
                'tools'       => $listToolsDraft
            ],
            'list_user'       => [
                'pj' => ['name' => $user->name, 'sign' => null]
            ],
            'exit_date'       => Carbon::now()->addDays(1),
            'is_returned'     => 0,
            'return_date'     => null,
            'keterangan'      => 'Menunggu persetujuan SPV untuk pengambilan material.',
            'foto'            => null,
        ]);

        $toolsForExt = $tools->random(min(3, $tools->count()));
        $listToolsExt = [];

        foreach ($toolsForExt as $t) {
            $maxQty = min($t->jumlah, 10);
            $listToolsExt[] = [
                'id'     => $t->tool_id,
                'nama'   => $t->nama,
                'satuan' => $t->satuan ?? 'Unit',
                'qty'    => rand(1, $maxQty)
            ];
        }

        HistoryTool::create([
            'organization_id' => $org->organization_id,
            'type'            => 'Eksternal',
            'status'          => 'approved',
            'created_by'      => $user->user_id,
            'list_tools'      => [
                'method_name' => null,
                'tools'       => $listToolsExt
            ],
            'list_user'       => [
                'receiver' => ['name' => 'PT Haleyora Power', 'sign' => null],
                'giver'    => ['name' => $user->name, 'sign' => null]
            ],
            'exit_date'       => Carbon::now()->subDays(2),
            'is_returned'     => 0,
            'return_date'     => null,
            'keterangan'      => 'Pekerjaan perampalan pohon (ROW) jalur pantura.',
            'foto'            => null,
        ]);

        $toolsForInt = $tools->random(min(1, $tools->count()));
        $listToolsInt = [];

        foreach ($toolsForInt as $t) {
            $maxQty = min($t->jumlah, 3);
            $listToolsInt[] = [
                'id'     => $t->tool_id,
                'nama'   => $t->nama,
                'satuan' => $t->satuan ?? 'Set',
                'qty'    => rand(1, $maxQty)
            ];
        }

        HistoryTool::create([
            'organization_id' => $org->organization_id,
            'type'            => 'Internal',
            'status'          => 'approved',
            'created_by'      => $user->user_id,
            'list_tools'      => [
                'method_name' => 'Inspeksi Gardu Distribusi',
                'tools'       => $listToolsInt
            ],
            'list_user'       => [
                'pj' => ['name' => 'Budi (Teknisi)', 'sign' => null]
            ],
            'exit_date'       => Carbon::now()->subHours(5),
            'is_returned'     => 0,
            'return_date'     => null,
            'keterangan'      => 'Sedang digunakan untuk manuver jaringan di GD-05.',
            'foto'            => null,
        ]);
    }
}
