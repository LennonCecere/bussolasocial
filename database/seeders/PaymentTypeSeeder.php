<?php

namespace Database\Seeders;

use App\Models\PaymentTypeModel;
use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentTypeModel::create([
            'name' => 'Pix',
            'description' => 'à vista',
            'installments' => 1,
        ]);

        PaymentTypeModel::create([
            'name' => 'Cartão de Crédito à Vista',
            'description' => 'Cartão de Crédito à Vista (1x)',
            'installments' => 1,
        ]);

        PaymentTypeModel::create([
            'name' => 'Cartão de Crédito Parcelado',
            'description' => 'Cartão de Crédito Parcelado (de 2 a 12 parcelas)',
            'installments' => 12,
        ]);
    }
}
