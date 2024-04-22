<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->text(),
            'price' => fake()->numberBetween(100,10000),
            'qty' => fake()->numberBetween(0,1000),
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'user_id' => User::factory(),
            'weight'=>fake()->numberBetween(20,1000),
            'discount' => fake()->numberBetween(0,80),
        ];
    }
}
