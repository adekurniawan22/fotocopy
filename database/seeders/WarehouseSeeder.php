<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;
use App\Models\Organization;
use Faker\Factory as Faker;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $organizations = Organization::all();

        foreach ($organizations as $org) {
            for ($i = 0; $i < 2; $i++) {
                
                $namaGudang = 'Gudang ' . chr(65 + $i); 

                Warehouse::firstOrCreate(
                    [
                        'warehouse_name'   => $namaGudang, 
                        'organization_id'  => $org->organization_id,
                    ],
                    [
                        'is_active' => $faker->boolean(85),
                    ]
                );
            }
        }
    }
}