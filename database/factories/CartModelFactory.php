<?php

namespace Database\Factories;

use App\Models\CartModel;
use App\Models\ProductModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CartModel>
 */
class CartModelFactory extends Factory
{
    protected $model = CartModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => ProductModel::factory()->create()->id,
            'quantity' => fake()->numberBetween(100, 200),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
