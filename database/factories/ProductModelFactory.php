<?php

namespace Database\Factories;

use App\Models\ProductModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductModel>
 */
class ProductModelFactory extends Factory
{
    protected $model = ProductModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'description' => fake()->sentence,
            'price_in_cents' => fake()->numberBetween(1000, 100000),
            'quantity_in_stock' => fake()->numberBetween(100, 200),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
