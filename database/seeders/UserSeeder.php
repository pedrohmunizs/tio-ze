<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Infrastructure\User\Models\UserModel;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Crie dados iniciais aqui
        // Exemplo:
        // UserModel::create([
        //     'name' => 'Exemplo',
        //     'email' => 'exemplo@email.com',
        //     'status' => 'active',
        // ]);
        
        // Ou use a factory
        // UserModel::factory(10)->create();
    }
}