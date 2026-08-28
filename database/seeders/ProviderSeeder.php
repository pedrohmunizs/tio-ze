<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Infrastructure\Provider\Models\ProviderModel;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        // Crie dados iniciais aqui
        // Exemplo:
        // ProviderModel::create([
        //     'name' => 'Exemplo',
        //     'email' => 'exemplo@email.com',
        //     'status' => 'active',
        // ]);
        
        // Ou use a factory
        // ProviderModel::factory(10)->create();
    }
}