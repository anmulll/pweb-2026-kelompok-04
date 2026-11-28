<?php

namespace Database\Factories;

use App\Models\Promotion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PromotionFactory extends Factory
{
    protected $model = Promotion::class;

    public function definition(): array
    {
        return [
            'created_by' => User::factory()->seller(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'is_active' => true,
        ];
    }
}
