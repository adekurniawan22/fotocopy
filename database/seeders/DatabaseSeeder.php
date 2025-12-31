<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $storage = Storage::disk('public');

        foreach ($storage->directories() as $directory) {
            $storage->deleteDirectory($directory);
        }

        $files = collect($storage->files())->reject(function ($file) {
            return $file === '.gitignore';
        })->toArray();

        $storage->delete($files);

        $this->call([
            RolesSeeder::class,
            OrganizationsSeeder::class,
            UserSeeder::class,
            WarehouseSeeder::class,
            ToolSeeder::class,
            // WorkPlanSeeder::class,
            PartnershipSeeder::class,
            MethodToolSeeder::class,
            HistoryToolSeeder::class,
            SpkiSeeder::class,
            OptionsSeeder::class,
            LaporanPekerjaanSeeder::class,
            GarduIndukSeeder::class,
            JaringanSeeder::class,
            // Tambahkan seeder lain di sini jika ada...
        ]);
    }
}
