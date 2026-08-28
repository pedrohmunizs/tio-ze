<?php

namespace App\Application\Provider\UseCases;

use App\Domain\Provider\Entities\Provider;
use App\Domain\Provider\Repositories\ProviderRepositoryInterface;
use App\Application\Provider\DTOs\ProviderData;

class UpdateProviderUseCase
{
    public function __construct(
        private ProviderRepositoryInterface $repository
    ) {}

    public function execute(int $id, ProviderData $data): Provider
    {
        $entity = $this->repository->findById($id);
        if (!$entity) {
            throw new \RuntimeException('Provider not found');
        }

        $entity->setData($data->toArray());
        $this->repository->save($entity);
        return $entity;
    }
}