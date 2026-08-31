<?php

namespace App\Application\TransportRequest\UseCases;

use App\Domain\TransportRequest\Entities\TransportRequest;
use App\Domain\TransportRequest\Repositories\TransportRequestRepositoryInterface;
use App\Application\TransportRequest\DTOs\CreateTransportRequestData;
use App\Domain\Route\Repositories\RouteRepositoryInterface;

class CreateTransportRequestUseCase
{
    public function __construct(
        private TransportRequestRepositoryInterface $repository,
        private RouteRepositoryInterface $routeRepository
    ) {}

    public function execute(CreateTransportRequestData $data): TransportRequest
    {
        $route = $this->routeRepository->findById($data->fk_route);

        if (!$route) {
            throw new \DomainException('Route not found');
        }

        $entity = new TransportRequest(
            fk_route: $route->getId(),
            fk_provider: $route->getProviderId(),
            fk_student: $data->fk_student
        );

        $this->repository->save($entity);
        return $entity;
    }
}