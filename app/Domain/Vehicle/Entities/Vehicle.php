<?php

namespace App\Domain\Vehicle\Entities;

use App\Domain\Provider\Entities\Provider;
use App\Domain\Vehicle\Enums\VehicleStatus;
use DateTimeImmutable;

class Vehicle
{
    private ?int $id;
    private string $brand;
    private string $model;
    private string $plate;
    private int $year;
    private int $capacity;
    private int $fk_provider;
    private VehicleStatus $status;
    private ?string $photo;
    private ?Provider $provider = null;
    private ?DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;
    private ?DateTimeImmutable $deletedAt = null;

    public function __construct(
        string $brand,
        string $model,
        string $plate,
        int $year,
        int $capacity,
        int $fk_provider,
        ?string $photo = null,
        VehicleStatus $status = VehicleStatus::ACTIVE,
        ?int $id = null)
    {
        $this->id = $id;
        $this->brand = $brand;
        $this->model = $model;
        $this->plate = $plate;
        $this->year = $year;
        $this->capacity = $capacity;
        $this->fk_provider = $fk_provider;
        $this->photo = $photo;
        $this->status = $status;
        $this->createdAt = new DateTimeImmutable();
    }

    public function loadProvider(Provider $provider): self
    {
        $this->provider = $provider;
        return $this;
    }

    public function getId(): ?int { return $this->id; }
    public function getProviderId(): int { return $this->fk_provider; }
    public function getBrand(): string { return $this->brand; }
    public function getModel(): string { return $this->model; }
    public function getPlate(): string { return $this->plate; }
    public function getYear(): int { return $this->year; }
    public function getCapacity(): int { return $this->capacity; }
    public function getPhoto(): ?string { return $this->photo; }
    public function getStatus(): VehicleStatus { return $this->status; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?DateTimeImmutable { return $this->updatedAt; }
    public function getDeletedAt(): ?DateTimeImmutable { return $this->deletedAt; }
    public function getProvider(): ?Provider { return $this->provider; }
    public function isActive(): bool { return $this->status === VehicleStatus::ACTIVE; }
    public function isInactive(): bool { return $this->status === VehicleStatus::INACTIVE; }
    public function isMaintenance(): bool { return $this->status === VehicleStatus::MAINTENANCE; }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function setDeletedAt(?DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    public function setStatus(string $status): self
    {
        $status = VehicleStatus::fromString($status);

        if ($this->status === $status) {
            throw new \DomainException("Vehicle is already {$status->value}");
        }

        $this->status = $status;
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'brand' => $this->brand,
            'model' => $this->model,
            'plate' => $this->plate,
            'year' => $this->year,
            'capacity' => $this->capacity,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'fk_provider' => $this->fk_provider,
            'photo' => $this->photo,
            'provider' => $this->provider?->toArray(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}