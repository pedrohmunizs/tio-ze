<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Infrastructure\Driver\Models\DriverModel;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        // Crie dados iniciais aqui
        // Exemplo:
        // DriverModel::create([
        //     'name' => 'Exemplo',
        //     'email' => 'exemplo@email.com',
        //     'status' => 'active',
        // ]);
        
        // Ou use a factory
        // DriverModel::factory(10)->create();
    }
}