<?php

namespace Database\Factories;

use App\Enums\ConditionGrade;
use App\Enums\FunctionalStatus;
use App\Enums\ProductCondition;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = 'iPhone ' . fake()->numberBetween(11, 15) . ' Pro';

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'model' => $name,
            'sku' => strtoupper(Str::random(10)),
            'storage' => '128GB',
            'color' => 'Space Black',
            'description' => fake()->paragraph(),
            'condition' => ProductCondition::NEW,
            'price' => fake()->randomFloat(2, 5000000, 25000000),
            'stock' => fake()->numberBetween(1, 20),
            'is_active' => true,
        ];
    }

    public function used(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => ProductCondition::USED,
            'stock' => 1,
            'condition_grade' => ConditionGrade::A,
            'battery_health' => fake()->numberBetween(80, 100),
            'functional_status' => FunctionalStatus::NORMAL->value,
            'defect_notes' => 'Mulus pemakaian wajar',
            'included_accessories' => 'Fullset Box',
            'warranty_info' => 'Garansi Toko 1 Bulan',
        ]);
    }
}
