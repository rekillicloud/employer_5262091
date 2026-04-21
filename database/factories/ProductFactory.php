<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'price' => fake()->randomFloat(2, 10, 2000),
            'category_id' => Category::factory(),
            'in_stock' => fake()->boolean(),
            'rating' => fake()->randomFloat(1, 0, 5),
        ];
    }
}
