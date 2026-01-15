<?php

namespace Database\Seeders;

use App\Models\Inventory;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $parts = [
            ['part_name' => 'Oli Mesin',     'quantity' => 50, 'price' => 45000,  'low_stock_level' => 3],
            ['part_name' => 'Filter Oli',    'quantity' => 30, 'price' => 25000,  'low_stock_level' => 3],
            ['part_name' => 'Busi',          'quantity' => 2, 'price' => 15000,  'low_stock_level' => 3],
            ['part_name' => 'Ban Depan',     'quantity' => 10, 'price' => 250000, 'low_stock_level' => 3],
            ['part_name' => 'Ban Belakang',  'quantity' => 10, 'price' => 280000, 'low_stock_level' => 3],
            ['part_name' => 'Kampas Rem',    'quantity' => 25, 'price' => 35000,  'low_stock_level' => 3],
            ['part_name' => 'Aki',           'quantity' => 15, 'price' => 400000, 'low_stock_level' => 3],
            ['part_name' => 'Rantai',        'quantity' => 20, 'price' => 120000, 'low_stock_level' => 3],
            ['part_name' => 'Shockbreaker',  'quantity' => 12, 'price' => 300000, 'low_stock_level' => 3],
            ['part_name' => 'Lampu Depan',   'quantity' => 18, 'price' => 80000,  'low_stock_level' => 3],
            ['part_name' => 'Lampu Belakang', 'quantity' => 18, 'price' => 90000,  'low_stock_level' => 3],
            ['part_name' => 'Servis Besar', 'quantity' => 1, 'price' => 600000,  'low_stock_level' => 3],

        ];

        foreach ($parts as $part) {
            Inventory::updateOrCreate(
                ['part_name' => $part['part_name']],
                $part
            );
        }
    }
}
