<?php

namespace Database\Factories;

use App\Models\PaymentTypeModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentTypeModel>
 */
class PaymentTypeModelFactory extends Factory
{
    protected $model = PaymentTypeModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word,
            'description' => fake()->sentence,
            'installments' => fake()->numberBetween(1, 36),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
