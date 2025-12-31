<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Partnership;
use App\Models\Organization;
use Faker\Factory as Faker;

class PartnershipSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $organizations = Organization::all();

        if ($organizations->count() > 0) {
            for ($i = 1; $i <= 10; $i++) {
                Partnership::create([
                    'organization_id' => $organizations->random()->organization_id,
                    'partnership_name' => $faker->company,
                    'penanggung_jawab' => $faker->name,
                    'alamat' => $faker->address,
                    'no_hp' => $faker->phoneNumber,
                ]);
            }
        }
    }
}
