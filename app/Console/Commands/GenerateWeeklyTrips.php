<?php

namespace App\Console\Commands;

use App\Application\Trip\UseCases\GenerateWeeklyTripUseCase;
use Illuminate\Console\Command;

class GenerateWeeklyTrips extends Command
{
    protected $signature = 'trips:generate-weekly';
    protected $description = 'Gera as viagens (trips) da semana baseado nas rotas ativas';

    public function handle(GenerateWeeklyTripUseCase $useCase)
    {
        try {
            $useCase->execute();

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Erro ao gerar viagens: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
