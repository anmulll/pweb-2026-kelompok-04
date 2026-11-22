<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Seller Demo
        User::firstOrCreate(
            ['email' => 'seller@istore.test'],
            [
                'name' => 'Seller Demo iStore',
                'password' => Hash::make('password'),
                'role' => UserRole::Seller,
                'phone' => '081234567890',
                'default_shipping_address' => 'iStore Apple Specialist Store, Jakarta Selatan',
            ]
        );

        // 2. Akun Buyer 1 Demo
        User::firstOrCreate(
            ['email' => 'buyer@istore.test'],
            [
                'name' => 'Budi Pratama (Buyer 1)',
                'password' => Hash::make('password'),
                'role' => UserRole::Buyer,
                'phone' => '089876543210',
                'default_shipping_address' => 'Jl. Perintis Kemerdekaan KM 10, Makassar, Sulawesi Selatan',
            ]
        );

        // 3. Akun Buyer 2 Demo
        User::firstOrCreate(
            ['email' => 'buyer2@istore.test'],
            [
                'name' => 'Siti Rahma (Buyer 2)',
                'password' => Hash::make('password'),
                'role' => UserRole::Buyer,
                'phone' => '081122334455',
                'default_shipping_address' => 'Jl. Malioboro No. 45, Danurejan, D.I. Yogyakarta',
            ]
        );
    }
}
