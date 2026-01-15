<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
 
        User::create([
            'name' => 'Admin MotorKita',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
        ]);

        User::create([
            'name' => 'Deroz Mekanik',
            'email' => 'deroz@motorkita.com',
            'password' => Hash::make('password'),
            'role' => 'mechanic',
            'phone' => '081234567891',
        ]);

        User::create([
            'name' => 'rafi MotorKita',
            'email' => 'rafi@motorkita',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '081234567892',
        ]);
    }
}