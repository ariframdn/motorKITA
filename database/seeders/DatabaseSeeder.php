<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Login (Admin, Mekanik, Customer)
        $users = [
            [
                'name' => 'Admin Bengkel',
                'email' => 'admin@motorkita.com',
                'password' => Hash::make('password'), // passwordnya: password
                'role' => 'admin',
                'phone' => '081234567890',
                'created_at' => now(),
            ],
            [
                'name' => 'Budi Mekanik',
                'email' => 'mekanik@motorkita.com',
                'password' => Hash::make('password'),
                'role' => 'mechanic',
                'phone' => '081111111111',
                'created_at' => now(),
            ],
            [
                'name' => 'Andi Customer',
                'email' => 'customer@motorkita.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '082222222222',
                'created_at' => now(),
            ]
        ];
        DB::table('users')->insert($users);

        // 2. Data Jasa Servis
        DB::table('service_catalogs')->insert([
            ['name' => 'Ganti Oli', 'price' => 50000, 'duration' => 30],
            ['name' => 'Tune Up', 'price' => 75000, 'duration' => 45],
            ['name' => 'Service CVT', 'price' => 85000, 'duration' => 60],
        ]);

        // 3. Data Sparepart
        DB::table('spare_parts')->insert([
            ['name' => 'Oli Yamalube', 'stock' => 50, 'price' => 60000],
            ['name' => 'Kampas Rem', 'stock' => 20, 'price' => 35000],
            ['name' => 'V-Belt', 'stock' => 10, 'price' => 120000],
        ]);

        // 4. Data Motor Customer
        DB::table('motorcycles')->insert([
            'user_id' => 3, // Punya si Andi Customer
            'brand' => 'Honda Vario',
            'plate_number' => 'D 1234 ABC',
            'year' => '2022',
            'created_at' => now(),
        ]);
    }
}