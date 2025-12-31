<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Organization;
use App\Models\Role;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $organizations = Organization::all();
        $roles = Role::all();

        $adminNip = 2;

        $roleCounts = [
            1 => 1, // Super Admin
            2 => 1, // Admin: 1 per organisasi
            3 => 1, // Assistant Manager
            4 => 2, // Team Leader
            5 => 1, // JTC
        ];

        $defaultCount = 2;

        foreach ($organizations as $org) {
            foreach ($roles as $role) {
                $count = $roleCounts[$role->role_id] ?? $defaultCount;

                if ($role->role_id == 1) {
                    if (!User::where('role_id', 1)->exists()) {
                        User::create([
                            'role_id'         => 1,
                            'organization_id' => $org->organization_id,
                            'name'            => "Ade Kurniawan",
                            'nip'             => '1',
                            'no_hp'           => '08' . $faker->numerify('##########'),
                            'password'        => '123',
                            'is_active'       => 1,
                        ]);
                    }
                    continue;
                }

                for ($i = 0; $i < $count; $i++) {
                    if ($role->role_id == 2) {
                        $nip = (string) $adminNip;
                        $adminNip++;
                    } else {
                        $nip = $faker->unique()->numerify('##########');
                    }

                    User::create([
                        'role_id'         => $role->role_id,
                        'organization_id' => $org->organization_id,
                        'name'            => $faker->name(),
                        'nip'             => $nip,
                        'no_hp'           => '08' . $faker->numerify('##########'),
                        'password'        => '123',
                        'is_active'       => 1,
                    ]);
                }
            }
        }
    }
}
