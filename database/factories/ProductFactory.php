<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->words(3, true),
            'slug' => fn (array $attributes) => Str::slug($attributes['name']),
            'sku' => fake()->unique()->bothify('SKU-####'),
            'short_description' => fake()->sentence(),
            'long_description' => fake()->paragraph(3),
            'price' => fake()->randomFloat(2, 10, 1000),
            'sale_price' => fn (array $attributes) => fake()->optional()->randomFloat(2, 5, $attributes['price']),
            'is_active' => true,
            'quantity' => fake()->numberBetween(0, 100),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Produto com dimensões definidas para cálculo de frete
     */
    public function withDimensions(): static
    {
        return $this->state(fn (array $attributes) => [
            'weight' => 0.5,
            'height' => 10,
            'width' => 20,
            'length' => 30,
        ]);
    }
}

