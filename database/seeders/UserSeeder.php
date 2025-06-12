<?php

namespace Database\Seeders;

use App\Models\UserModel;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserModel::create([
            'name' => 'Lennon Cécere',
            'email' => 'lennon@gmail.com',
            'password' => bcrypt('Bussola#123@'),
        ]);
    }
}
