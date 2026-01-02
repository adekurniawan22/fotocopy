<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Item;
use App\Models\Option;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Admin User
        User::create([
            'name' => 'Administrator',
            'user_name' => 'admin',
            'password' => Hash::make('123'),
        ]);

        $faker = Faker::create('id_ID');

        // 2. Daftar Barang Realistis (ATK & Fotocopy)
        $items = [
            ['name' => 'Kertas HVS A4 70gsm Sidu', 'unit' => 'Rim', 'base_price' => 42000],
            ['name' => 'Kertas HVS F4 70gsm Sidu', 'unit' => 'Rim', 'base_price' => 48000],
            ['name' => 'Kertas HVS A4 80gsm PaperOne', 'unit' => 'Rim', 'base_price' => 55000],
            ['name' => 'Kertas Foto Glossy A4 210gsm', 'unit' => 'Pack', 'base_price' => 35000],
            ['name' => 'Kertas Buffalo Warna Campur', 'unit' => 'Pack', 'base_price' => 25000],
            ['name' => 'Pulpen Standard AE7 Hitam', 'unit' => 'Pcs', 'base_price' => 2500],
            ['name' => 'Pulpen Pilot Ballliner Hitam', 'unit' => 'Pcs', 'base_price' => 14000],
            ['name' => 'Pensil 2B Faber Castell', 'unit' => 'Pcs', 'base_price' => 4000],
            ['name' => 'Penghapus Joyko Hitam Kecil', 'unit' => 'Pcs', 'base_price' => 1000],
            ['name' => 'Spidol Boardmarker Snowman Hitam', 'unit' => 'Pcs', 'base_price' => 8500],
            ['name' => 'Tipe-X Kertas (Correction Tape) Kenko', 'unit' => 'Pcs', 'base_price' => 6000],
            ['name' => 'Stapler HD-10 Kenko', 'unit' => 'Pcs', 'base_price' => 12000],
            ['name' => 'Isi Staples No. 10 (Kecil)', 'unit' => 'Box', 'base_price' => 2500],
            ['name' => 'Lakban Bening Daimaru 2 Inch', 'unit' => 'Roll', 'base_price' => 11000],
            ['name' => 'Lakban Hitam Kain', 'unit' => 'Roll', 'base_price' => 15000],
            ['name' => 'Lem Fox Stick 60gr', 'unit' => 'Pcs', 'base_price' => 8000],
            ['name' => 'Gunting Besar Joyko', 'unit' => 'Pcs', 'base_price' => 15000],
            ['name' => 'Cutter Besar Kenko L-500', 'unit' => 'Pcs', 'base_price' => 18000],
            ['name' => 'Map Plastik Button Folder Clear', 'unit' => 'Pcs', 'base_price' => 3000],
            ['name' => 'Map Kertas Batik', 'unit' => 'Pcs', 'base_price' => 1500],
            ['name' => 'Ordner Bantex Kwitansi', 'unit' => 'Pcs', 'base_price' => 35000],
            ['name' => 'Buku Tulis Sidu 38 Lembar', 'unit' => 'Pack', 'base_price' => 32000],
            ['name' => 'Flashdisk SanDisk 32GB Cruzer Blade', 'unit' => 'Pcs', 'base_price' => 65000],
            ['name' => 'Mouse Logitech B100 Optical', 'unit' => 'Pcs', 'base_price' => 55000],
            ['name' => 'Baterai ABC Alkaline AA (Isi 2)', 'unit' => 'Set', 'base_price' => 12000],
            ['name' => 'Printer Epson L1210 EcoTank', 'unit' => 'Unit', 'base_price' => 2100000],
        ];

        foreach ($items as $item) {
            $margin = $faker->randomFloat(2, 0.1, 0.3);
            $sellPriceRaw = $item['base_price'] + ($item['base_price'] * $margin);

            $sellPrice = ceil($sellPriceRaw / 100) * 100;

            Item::create([
                'item_name'   => $item['name'],
                'foto'        => [],
                'buy_price'   => $item['base_price'],
                'sell_price'  => (int) $sellPrice,
                'unit'        => $item['unit'],
                'letak'       => 'Rak ' . $faker->randomElement(['A', 'B', 'C']) . '-' . $faker->numberBetween(1, 5),
                'location'    => $faker->randomElement(['Etalase Depan', 'Gudang Belakang', 'Rak Dinding', 'Laci Kasir']),
                'description' => 'Stok tersedia untuk ' . $item['name'] . '. Kualitas terjamin.',
            ]);
        }

        // 3. Seeder Option 
        Option::create([
            'option_name' => 'app_name',
            'text_value' => 'Setting Nota Toko',
        ]);
    }
}
