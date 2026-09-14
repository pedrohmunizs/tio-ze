<?php

namespace App\Domain\Stop\Services;

use App\Application\Route\UseCases\OptimizePickupRouteUseCase;
use App\Application\Stop\UseCases\CreateStopUseCase;
use App\Application\StopChild\UseCases\CreateStopChildUseCase;
use App\Domain\Stop\Repositories\StopRepositoryInterface;
use App\Domain\StopChild\Repositories\StopChildRepositoryInterface;

class StopService
{
    public function __construct(
        private StopRepositoryInterface $repository,
        private StopChildRepositoryInterface $stop_child_repository,
        private CreateStopUseCase $create_stop_use_case,
        private CreateStopChildUseCase $create_stop_child_use_case,
        private OptimizePickupRouteUseCase $optimize_route_use_case,
    ) {}

    public function resolveStopsChild(int $fk_child, int $fk_address, string $zip_code, string $number)
    {
        $stops = $this->repository->findByChildId($fk_child);

        foreach ($stops as $stop) {

            $count_child_stop = $this->stop_child_repository->count(['fk_stop' => $stop['id']]);

            if ($count_child_stop == 1) {
                $this->repository->delete($stop['id']);
            } else {
                $this->stop_child_repository->deleteByStopIdAndChildId($stop['id'], $fk_child);
            }

            $fk_stop = $this->repository->existsStopByAddress($stop['fk_route'], $zip_code, $number, $stop['type']);

            if (!$fk_stop) {
                $this->create_stop_use_case->execute(
                    fk_route: $stop['fk_route'],
                    fk_student: $fk_child,
                    fk_address: $fk_address,
                    type: $stop['type'],
                );

                $this->optimize_route_use_case->execute($stop['fk_route'], $stop['type']);
            } else {
                $stop_order = $this->stop_child_repository->getMaxOrder($fk_stop);

                $this->create_stop_child_use_case->execute(
                    fk_stop: $fk_stop,
                    fk_child: $fk_child,
                    stop_order: ($stop_order + 1)
                );
            }
        }
    }
}