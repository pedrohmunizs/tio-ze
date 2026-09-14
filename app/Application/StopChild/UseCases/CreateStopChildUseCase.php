<?php

namespace App\Application\StopChild\UseCases;

use App\Domain\StopChild\Entities\StopChild;
use App\Domain\StopChild\Repositories\StopChildRepositoryInterface;

class CreateStopChildUseCase
{
    public function __construct(
        private StopChildRepositoryInterface $repository
    ) {}

    public function execute(int $fk_stop, int $fk_child, int $stop_order): StopChild
    {
        $entity = new StopChild(
            fk_stop: $fk_stop,
            fk_child: $fk_child,
            stop_order: $stop_order,
        );

        $this->repository->save($entity);

        return $entity;
    }
}