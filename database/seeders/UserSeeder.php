<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test customer
        User::create([
            'name' => 'Customer Test',
            'email' => 'customer@rental.com',
            'phone' => '081234567892',
            'address' => 'Jl. Customer No. 3, Jakarta',
            'identity_number' => '3171234567890125',
            'role' => 'customer',
            'email_verified_at' => now(),
            'password' => Hash::make('customer123'),
        ]);

        // Generate additional customers
        User::factory(10)->create(['role' => 'customer']);
    }
}
