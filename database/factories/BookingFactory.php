<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 month', '+1 month');
        $endDate = fake()->dateTimeBetween($startDate, '+1 week');
        $totalDays = $startDate->diff($endDate)->days + 1;
        $pricePerDay = fake()->numberBetween(200000, 800000);

        return [
            'user_id' => User::where('role', 'customer')->inRandomOrder()->first()?->id ?? User::factory()->create(['role' => 'customer'])->id,
            'car_id' => Car::inRandomOrder()->first()?->id ?? Car::factory()->create()->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => $totalDays,
            'total_price' => $totalDays * $pricePerDay,
            'status' => fake()->randomElement(['pending', 'confirmed', 'ongoing', 'completed']),
            'pickup_location' => fake()->address(),
            'return_location' => fake()->address(),
            'notes' => fake()->optional()->sentence()
        ];
    }
}
