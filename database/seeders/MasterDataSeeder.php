<?php

namespace Database\Seeders;

use App\Enums\ConditionGrade;
use App\Enums\FunctionalStatus;
use App\Enums\ProductCondition;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Users (Seller & Buyer)
        $seller = User::firstOrCreate(
            ['email' => 'seller@istore.test'],
            [
                'name' => 'Adel Seller',
                'password' => Hash::make('password'),
                'role' => UserRole::Seller,
                'phone' => '081234567890',
            ]
        );

        $buyer = User::firstOrCreate(
            ['email' => 'buyer@istore.test'],
            [
                'name' => 'Budi Buyer',
                'password' => Hash::make('password'),
                'role' => UserRole::Buyer,
                'phone' => '089876543210',
                'address' => 'Jl. Perintis Kemerdekaan KM 10, Makassar, Sulawesi Selatan',
            ]
        );

        // 2. Create Categories
        $categoriesData = [
            ['name' => 'iPhone', 'slug' => 'iphone', 'description' => 'Smartphone Apple canggih dengan sistem kamera mutakhir.'],
            ['name' => 'MacBook', 'slug' => 'macbook', 'description' => 'Laptop bertenaga Apple Silicon untuk produktivitas tinggi.'],
            ['name' => 'iPad', 'slug' => 'ipad', 'description' => 'Tablet serbaguna untuk kreasi, belajar, dan hiburan.'],
            ['name' => 'Apple Watch', 'slug' => 'apple-watch', 'description' => 'Pendamping kesehatan dan kebugaran di pergelangan tangan Anda.'],
            ['name' => 'AirPods', 'slug' => 'airpods', 'description' => 'Earphone dan headphone nirkabel dengan audio spasial.'],
            ['name' => 'Aksesori', 'slug' => 'aksesori', 'description' => 'Pengisi daya MagSafe, kabel, dan pelindung original Apple.'],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['slug']] = Category::firstOrCreate(
                ['slug' => $cat['slug']],
                array_merge($cat, ['is_active' => true])
            );
        }

        // 3. Create Products (Brand New & Second-hand Units)
        $productsData = [
            // iPhone Baru
            [
                'category_id' => $categories['iphone']->id,
                'name' => 'iPhone 15 Pro Max 256GB Natural Titanium',
                'model' => 'iPhone 15 Pro Max',
                'sku' => 'IP15PM-256-NT',
                'storage' => '256GB',
                'color' => 'Natural Titanium',
                'description' => "Didesain dengan titanium tangguh dan ringan kelas kedirgantaraan. Dilengkapi chip A17 Pro revolusioner dan tombol Tindakan yang dapat disesuaikan.",
                'condition' => ProductCondition::NEW,
                'price' => 22999000,
                'stock' => 12,
                'is_active' => true,
            ],
            // MacBook Baru
            [
                'category_id' => $categories['macbook']->id,
                'name' => 'MacBook Pro 14" M3 Pro 512GB Space Black',
                'model' => 'MacBook Pro 14"',
                'sku' => 'MBP14-M3P-512-SB',
                'storage' => '512GB',
                'color' => 'Space Black',
                'description' => "Laptop pro bertenaga chip M3 Pro. Layar Liquid Retina XDR memukau, baterai tahan hingga 18 jam, dan port lengkap.",
                'condition' => ProductCondition::NEW,
                'price' => 31499000,
                'stock' => 5,
                'is_active' => true,
            ],
            // iPad Baru
            [
                'category_id' => $categories['ipad']->id,
                'name' => 'iPad Air 11" M2 128GB Starlight',
                'model' => 'iPad Air 11"',
                'sku' => 'IPAD-AIR-M2-128-SL',
                'storage' => '128GB',
                'color' => 'Starlight',
                'description' => "Didukung chip M2 secepat kilat. Kompatibel dengan Apple Pencil Pro dan Magic Keyboard baru.",
                'condition' => ProductCondition::NEW,
                'price' => 11999000,
                'stock' => 8,
                'is_active' => true,
            ],
            // Apple Watch Baru
            [
                'category_id' => $categories['apple-watch']->id,
                'name' => 'Apple Watch Series 9 45mm Midnight Aluminium',
                'model' => 'Apple Watch Series 9',
                'sku' => 'AWS9-45-MID',
                'storage' => '64GB',
                'color' => 'Midnight',
                'description' => "Lebih cerdas, lebih cerah, dan lebih bertenaga berkat chip S9 SiP. Gestur ketuk dua kali magis.",
                'condition' => ProductCondition::NEW,
                'price' => 6999000,
                'stock' => 10,
                'is_active' => true,
            ],
            // AirPods Baru
            [
                'category_id' => $categories['airpods']->id,
                'name' => 'AirPods Pro Gen 2 USB-C',
                'model' => 'AirPods Pro 2',
                'sku' => 'APP2-USBC',
                'storage' => null,
                'color' => 'White',
                'description' => "Peredam Kebisingan Aktif hingga 2x lebih baik. Audio Spasial Adaptif dan casing pengisian daya USB-C dengan Presisi Finding.",
                'condition' => ProductCondition::NEW,
                'price' => 3799000,
                'stock' => 20,
                'is_active' => true,
            ],

            // Unit Second 1: iPhone 14 Pro (Grade A)
            [
                'category_id' => $categories['iphone']->id,
                'name' => 'iPhone 14 Pro 128GB Deep Purple (Second Terinspeksi)',
                'model' => 'iPhone 14 Pro',
                'sku' => 'IP14P-128-DP-SEC01',
                'storage' => '128GB',
                'color' => 'Deep Purple',
                'description' => "Unit second resmi eks iBox. Dynamic Island, kamera 48 MP, dan layar Always-On. Lolos pengujian 32 titik sensor teknisi.",
                'condition' => ProductCondition::USED,
                'condition_grade' => ConditionGrade::A,
                'battery_health' => 91,
                'functional_status' => FunctionalStatus::NORMAL->value,
                'defect_notes' => 'Fisik mulus 98%, lecet halus minor tidak terlihat saat terpasang case.',
                'included_accessories' => 'Fullset Box + Kabel Lightning Original bawaan',
                'warranty_info' => 'Garansi Toko iStore 1 Bulan (Tukar Unit jika ada kendala hardware)',
                'price' => 12850000,
                'stock' => 1, // 1 unit = 1 SKU unik
                'is_active' => true,
            ],
            // Unit Second 2: iPhone 13 (Grade B)
            [
                'category_id' => $categories['iphone']->id,
                'name' => 'iPhone 13 128GB Midnight (Second Terinspeksi)',
                'model' => 'iPhone 13',
                'sku' => 'IP13-128-MID-SEC02',
                'storage' => '128GB',
                'color' => 'Midnight',
                'description' => "Unit second ex garansi resmi Indonesia. Kamera ganda dengan mode Sinematik, chip A15 Bionic cepat dan hemat daya.",
                'condition' => ProductCondition::USED,
                'condition_grade' => ConditionGrade::B,
                'battery_health' => 86,
                'functional_status' => FunctionalStatus::NORMAL->value,
                'defect_notes' => 'Ada goresan halus wajar di pojok bezel kanan bawah, layar bebas shadow dan True Tone aktif.',
                'included_accessories' => 'Unit Only + OEM Fast Charging Cable',
                'warranty_info' => 'Garansi Toko iStore 1 Bulan',
                'price' => 7950000,
                'stock' => 1,
                'is_active' => true,
            ],
            // Unit Second 3: MacBook Air M1 (Grade A)
            [
                'category_id' => $categories['macbook']->id,
                'name' => 'MacBook Air 13" M1 256GB Space Grey (Second Terinspeksi)',
                'model' => 'MacBook Air 13" M1',
                'sku' => 'MBA-M1-256-SG-SEC03',
                'storage' => '256GB',
                'color' => 'Space Grey',
                'description' => "Laptop tanpa kipas yang senyap dan super kencang. Cycle Count baterai rendah (68x), keyboard US layout nyaman.",
                'condition' => ProductCondition::USED,
                'condition_grade' => ConditionGrade::A,
                'battery_health' => 96,
                'functional_status' => FunctionalStatus::NORMAL->value,
                'defect_notes' => 'Mulus 99% seperti baru, bebas dent maupun baret.',
                'included_accessories' => 'Fullset Box + Adaptor USB-C 30W Original Apple',
                'warranty_info' => 'Garansi Toko iStore 1 Bulan',
                'price' => 9850000,
                'stock' => 1,
                'is_active' => true,
            ],
        ];

        foreach ($productsData as $prod) {
            Product::firstOrCreate(
                ['sku' => $prod['sku']],
                $prod
            );
        }

        // 4. Create Active Promotions
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
        ];

        foreach ($promotionsData as $promo) {
            Promotion::firstOrCreate(
                ['title' => $promo['title']],
                $promo
            );
        }
    }
}
