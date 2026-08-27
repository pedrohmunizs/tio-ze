<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Infrastructure\Child\Models\ChildModel;

class ChildSeeder extends Seeder
{
    public function run(): void
    {
        // Crie dados iniciais aqui
        // Exemplo:
        // ChildModel::create([
        //     'name' => 'Exemplo',
        //     'email' => 'exemplo@email.com',
        //     'status' => 'active',
        // ]);
        
        // Ou use a factory
        // ChildModel::factory(10)->create();
    }
}