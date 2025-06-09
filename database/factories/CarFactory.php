<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $brands = ['Toyota', 'Honda', 'Suzuki', 'Daihatsu', 'Mitsubishi', 'Nissan'];
        $models = [
            'Toyota' => ['Avanza', 'Innova', 'Fortuner', 'Camry', 'Yaris'],
            'Honda' => ['Brio', 'Jazz', 'City', 'Civic', 'CR-V'],
            'Suzuki' => ['Ertiga', 'Swift', 'Baleno', 'Jimny'],
            'Daihatsu' => ['Xenia', 'Terios', 'Ayla', 'Sigra'],
            'Mitsubishi' => ['Pajero', 'Outlander', 'Mirage', 'Xpander'],
            'Nissan' => ['Grand Livina', 'X-Trail', 'Serena', 'March']
        ];

        $brand = fake()->randomElement($brands);
        $model = fake()->randomElement($models[$brand]);

        return [
            'brand' => $brand,
            'model' => $model,
            'year' => fake()->numberBetween(2015, 2024),
            'license_plate' => fake()->regexify('[A-Z]{1,2} \d{1,4} [A-Z]{1,3}'),
            'color' => fake()->randomElement(['Putih', 'Hitam', 'Silver', 'Merah', 'Biru', 'Abu-abu']),
            'transmission' => fake()->randomElement(['manual', 'automatic']),
            'fuel_type' => fake()->randomElement(['Bensin', 'Solar', 'Hybrid']),
            'seats' => fake()->randomElement([5, 7, 8]),
            'price_per_day' => fake()->numberBetween(200000, 800000),
            'status' => fake()->randomElement(['available', 'available', 'available', 'rented', 'maintenance']),
            'description' => fake()->paragraph(),
            'image_path' => 'cars/default.jpg'
        ];
    }
}
