<?php

namespace Database\Seeders;

use App\Models\Car;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cars = [
            [
                'brand' => 'Toyota',
                'model' => 'Avanza',
                'year' => 2022,
                'license_plate' => 'B 1234 ABC',
                'color' => 'Putih',
                'transmission' => 'manual',
                'fuel_type' => 'Bensin',
                'seats' => 7,
                'price_per_day' => 350000,
                'status' => 'available',
                'description' => 'Toyota Avanza 2022 kondisi terawat, cocok untuk keluarga'
            ],
            [
                'brand' => 'Honda',
                'model' => 'Brio',
                'year' => 2021,
                'license_plate' => 'B 5678 DEF',
                'color' => 'Merah',
                'transmission' => 'automatic',
                'fuel_type' => 'Bensin',
                'seats' => 5,
                'price_per_day' => 300000,
                'status' => 'available',
                'description' => 'Honda Brio automatic, irit BBM dan nyaman dikendarai'
            ],
            [
                'brand' => 'Toyota',
                'model' => 'Innova',
                'year' => 2023,
                'license_plate' => 'B 9012 GHI',
                'color' => 'Silver',
                'transmission' => 'automatic',
                'fuel_type' => 'Solar',
                'seats' => 8,
                'price_per_day' => 500000,
                'status' => 'available',
                'description' => 'Toyota Innova Reborn diesel automatic, sangat nyaman untuk perjalanan jauh'
            ],
            [
                'brand' => 'Suzuki',
                'model' => 'Ertiga',
                'year' => 2022,
                'license_plate' => 'B 3456 JKL',
                'color' => 'Hitam',
                'transmission' => 'manual',
                'fuel_type' => 'Bensin',
                'seats' => 7,
                'price_per_day' => 320000,
                'status' => 'available',
                'description' => 'Suzuki Ertiga manual, kabin luas dan bagasi besar'
            ],
            [
                'brand' => 'Honda',
                'model' => 'CR-V',
                'year' => 2023,
                'license_plate' => 'B 7890 MNO',
                'color' => 'Putih',
                'transmission' => 'automatic',
                'fuel_type' => 'Bensin',
                'seats' => 5,
                'price_per_day' => 650000,
                'status' => 'available',
                'description' => 'Honda CR-V 2023, SUV mewah dengan fitur lengkap'
            ]
        ];

        foreach ($cars as $carData) {
            Car::create($carData);
        }

        // Generate additional random cars
        Car::factory(15)->create();
    }
}
