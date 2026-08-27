<?php

namespace App\Application\School\UseCases;

use App\Domain\School\Entities\School;
use App\Domain\School\Repositories\SchoolRepositoryInterface;
use App\Application\School\DTOs\SchoolData;

class UpdateSchoolUseCase
{
    public function __construct(
        private SchoolRepositoryInterface $repository
    ) {}

    public function execute(int $id, SchoolData $data): School
    {
        $entity = $this->repository->findById($id);
        if (!$entity) {
            throw new \RuntimeException('School not found');
        }

        $entity->setData($data->toArray());
        $this->repository->save($entity);
        return $entity;
    }
}