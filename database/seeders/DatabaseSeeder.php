<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Guard: Tolak eksekusi data demo di lingkungan production
        if (app()->environment('production')) {
            $this->command->warn('Peringatan: Demo seeder diblokir pada lingkungan Production!');
            return;
        }

        // Panggil seeder secara berurutan sesuai dependency
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            PromotionSeeder::class,
            OrderDemoSeeder::class,
        ]);
    }
}
