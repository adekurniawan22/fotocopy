<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['role_name' => 'Super Admin']);
        Role::create(['role_name' => 'Admin Sistem']);
        Role::create(['role_name' => 'Assistant Manager']);
        Role::create(['role_name' => 'Team Leader']);
        Role::create(['role_name' => 'JTC']);
    }
}
