<?php

namespace Database\Seeders;

use App\Models\Promotion;
use App\Models\User;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        $seller = User::where('role', \App\Enums\UserRole::Seller)->first();

        if (! $seller) {
            return;
        }

        $promotionsData = [
            [
                'created_by' => $seller->id,
                'title' => 'Festival Apple Original: Garansi Toko & Unit Terinspeksi',
                'description' => 'Dapatkan cashback langsung hingga Rp 1.500.000 untuk pembelian seluruh lini iPhone dan MacBook baru maupun second.',
                'start_date' => now()->subDays(2)->toDateString(),
                'end_date' => now()->addDays(20)->toDateString(),
                'is_active' => true,
            ],
            [
                'created_by' => $seller->id,
                'title' => 'Beli Unit Second Tanpa Ragu di iStore',
                'description' => 'Transparansi penuh: Battery Health asli, foto detail, grade terstandarisasi, dan jaminan tukar unit 1 bulan.',
                'start_date' => now()->subDays(5)->toDateString(),
                'end_date' => now()->addDays(15)->toDateString(),
                'is_active' => true,
            ],
            [
                'created_by' => $seller->id,
                'title' => 'Promo Spesial Akhir Tahun (Kedaluwarsa)',
                'description' => 'Promo diskon akhir tahun terdahulu yang sudah selesai masa berlakunya.',
                'start_date' => now()->subDays(60)->toDateString(),
                'end_date' => now()->subDays(30)->toDateString(),
                'is_active' => false,
            ],
        ];

        foreach ($promotionsData as $promo) {
            Promotion::firstOrCreate(
                ['title' => $promo['title']],
                $promo
            );
        }
    }
}
