<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Organization;

class OptionsSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = Organization::all();

        $ewsData = [];

        foreach ($organizations as $org) {
            $ewsData[$org->organization_id] = [
                'keparahan' => [
                    'low'    => "365",
                    'medium' => "180",
                    'high'   => "90"
                ],
                'geografis' => [
                    'low'    => "10",
                    'medium' => "20",
                    'high'   => "30"
                ],
                'usia' => [
                    'low'    => "10",
                    'medium' => "20",
                    'high'   => "30"
                ],
                'material' => [
                    'low'    => "10",
                    'medium' => "20",
                    'high'   => "30"
                ]
            ];
        }

        DB::table('options')->insert([
            'option_id'   => 1,
            'option_name' => 'Setting Template PDF History Tool',
            'text_value'  => json_encode([], JSON_PRETTY_PRINT),
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);

        DB::table('options')->insert([
            'option_id'   => 2,
            'option_name' => 'Setting Template PDF SPKI',
            'text_value'  => json_encode([], JSON_PRETTY_PRINT),
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);

        DB::table('options')->insert([
            'option_id'   => 3,
            'option_name' => 'Setting Template PDF Laporan Pekerjaan',
            'text_value'  => json_encode([], JSON_PRETTY_PRINT),
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);

        DB::table('options')->insert([
            'option_id'   => 4,
            'option_name' => 'Setting Template EWS',
            'text_value'  => json_encode($ewsData, JSON_PRETTY_PRINT),
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);
    }
}