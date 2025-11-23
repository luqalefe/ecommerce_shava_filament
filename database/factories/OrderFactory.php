<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Endereco;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'endereco_id' => Endereco::factory(),
            'status' => 'pending',
            'total_amount' => fake()->randomFloat(2, 50, 1000),
            'shipping_cost' => fake()->randomFloat(2, 5, 50),
            'shipping_service' => fake()->randomElement(['Sedex', 'PAC', 'Jadlog']),
            'payment_method' => fake()->randomElement(['pix', 'mercadopago']),
            'payment_id' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processing',
        ]);
    }
}

