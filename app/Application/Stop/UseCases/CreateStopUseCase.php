<?php

namespace App\Application\Stop\UseCases;

use App\Domain\Stop\Entities\Stop;
use App\Domain\Stop\Enums\StopType;
use App\Domain\Stop\Repositories\StopRepositoryInterface;
use App\Domain\StopChild\Entities\StopChild;
use App\Domain\StopChild\Repositories\StopChildRepositoryInterface;

class CreateStopUseCase
{
    public function __construct(
        private StopRepositoryInterface $repository,
        private StopChildRepositoryInterface $stopChildRepository,
    ) {}

    public function execute(int $fk_route, int $fk_student, int $fk_address, string $type): Stop
    {
        $maxOrder = $this->repository->getMaxOrder($fk_route, $type);
        $nextOrder = $maxOrder + 1;

        $entity = new Stop(
            fk_route: $fk_route,
            fk_address: $fk_address,
            stop_order: $nextOrder,
            type: StopType::from($type),
        );

        $this->repository->save($entity);

        $stop_child = new StopChild(
            fk_stop: $entity->getId(),
            fk_child: $fk_student,
            stop_order: $nextOrder,
        );

        $this->stopChildRepository->save($stop_child);

        return $entity;
    }
}