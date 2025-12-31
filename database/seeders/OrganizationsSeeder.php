<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use Faker\Factory as Faker;

class OrganizationsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        Organization::firstOrCreate(
            ['organization_id' => 1],
            ['organization_name' => 'Unit Kalimantan Tengah']
        );

        Organization::firstOrCreate(
            ['organization_id' => 2],
            ['organization_name' => 'Unit Kalimantan Timur']
        );
    }
}
