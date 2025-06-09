<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@rental.com',
            'phone' => '081234567890',
            'address' => 'Jl. Admin No. 1, Jakarta',
            'identity_number' => '3171234567890123',
            'role' => 'admin',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
        ]);

        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@rental.com',
            'phone' => '081234567891',
            'address' => 'Jl. Super Admin No. 2, Jakarta',
            'identity_number' => '3171234567890124',
            'role' => 'admin',
            'email_verified_at' => now(),
            'password' => Hash::make('superadmin123'),
        ]);
    }
}
