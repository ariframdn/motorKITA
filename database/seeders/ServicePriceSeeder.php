<?php

namespace Database\Seeders;

use App\Models\ServicePrice;
use Illuminate\Database\Seeder;

class ServicePriceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'service_name' => 'Servis Rutin',
                'description' => 'Servis berkala standar',
                'price' => 50000,
                'is_active' => true,
            ],
            [
                'service_name' => 'Ganti Oli',
                'description' => 'Ganti oli mesin',
                'price' => 35000,
                'is_active' => true,
            ],
            [
                'service_name' => 'Tune Up',
                'description' => 'Tune up mesin',
                'price' => 75000,
                'is_active' => true,
            ],
            [
                'service_name' => 'Ganti Ban',
                'description' => 'Ganti ban depan/belakang',
                'price' => 150000,
                'is_active' => true,
            ],
            [
                'service_name' => 'Perbaikan Mesin',
                'description' => 'Perbaikan mesin motor',
                'price' => 200000,
                'is_active' => true,
            ],
            [
                'service_name' => 'Ganti Kampas Rem',
                'description' => 'Ganti kampas rem depan/belakang',
                'price' => 80000,
                'is_active' => true,
            ],
            [
                'service_name' => 'Service Besar',
                'description' => 'Service lengkap motor',
                'price' => 300000,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            ServicePrice::create($service);
        }
    }
}
