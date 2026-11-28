<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categoriesData = [
            ['name' => 'iPhone', 'slug' => 'iphone', 'description' => 'Smartphone Apple canggih dengan sistem kamera mutakhir.'],
            ['name' => 'MacBook', 'slug' => 'macbook', 'description' => 'Laptop bertenaga Apple Silicon untuk produktivitas tinggi.'],
            ['name' => 'iPad', 'slug' => 'ipad', 'description' => 'Tablet serbaguna untuk kreasi, belajar, dan hiburan.'],
            ['name' => 'Apple Watch', 'slug' => 'apple-watch', 'description' => 'Pendamping kesehatan dan kebugaran di pergelangan tangan Anda.'],
            ['name' => 'AirPods', 'slug' => 'airpods', 'description' => 'Earphone dan headphone nirkabel dengan audio spasial.'],
            ['name' => 'Aksesori', 'slug' => 'aksesori', 'description' => 'Pengisi daya MagSafe, kabel, dan pelindung original Apple.'],
        ];

        foreach ($categoriesData as $cat) {
            Category::firstOrCreate(
                ['slug' => $cat['slug']],
                array_merge($cat, ['is_active' => true])
            );
        }
    }
}
