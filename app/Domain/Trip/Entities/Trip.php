<?php

namespace App\Domain\Trip\Entities;

use App\Domain\Driver\Entities\Driver;
use App\Domain\Route\Entities\Route;
use App\Domain\Trip\Enums\TripStatus;
use App\Domain\Trip\Enums\TripType;
use App\Domain\Vehicle\Entities\Vehicle;
use Carbon\Carbon;
use DateTimeImmutable;

class Trip
{
    private int $fk_route;
    private int $fk_vehicle;
    private int $fk_driver;
    private Carbon $date;
    private ?TripType $type;
    private ?TripStatus $status;
    private ?Route $route;
    private ?Vehicle $vehicle;
    private ?Driver $driver;
    private ?int $id;
    private ?DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;
    private ?DateTimeImmutable $startedAt = null;
    private ?DateTimeImmutable $completedAt = null;

    public function __construct(
        int $fk_route,
        int $fk_vehicle,
        int $fk_driver,
        Carbon $date,
        TripType $type,
        TripStatus $status = TripStatus::SCHEDULED,
        ?int $id = null
    )
    {
        $this->fk_route = $fk_route;
        $this->fk_vehicle = $fk_vehicle;
        $this->fk_driver = $fk_driver;
        $this->date = $date;
        $this->type = $type;
        $this->status = $status;
        $this->id = $id;
        $this->createdAt = new DateTimeImmutable();
    }

    public function loadRoute(Route $route): self
    {
        $this->route = $route;
        return $this;
    }

    public function loadVehicle(Vehicle $vehicle): self
    {
        $this->vehicle = $vehicle;
        return $this;
    }

    public function loadDriver(Driver $driver): self
    {
        $this->driver = $driver;
        return $this;
    }

    public function getId(): ?int { return $this->id; }
    public function getRouteId(): int { return $this->fk_route; }
    public function getVehicleId(): int { return $this->fk_vehicle; }
    public function getDriverId(): int { return $this->fk_driver; }
    public function getDate(): Carbon { return $this->date; }
    public function getStatus(): TripStatus { return $this->status; }
    public function getType(): TripType { return $this->type; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?DateTimeImmutable { return $this->updatedAt; }
    public function getStartedAt(): ?DateTimeImmutable { return $this->startedAt; }
    public function getCompletedAt(): ?DateTimeImmutable { return $this->completedAt; }
    public function getRoute(): ?Route { return $this->route; }
    public function getVehicle(): ?Vehicle { return $this->vehicle; }
    public function getDriver(): ?Driver { return $this->driver; }

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

    public function setStatus(string $status): self
    {
        $status = TripStatus::fromString($status);

        if ($this->status === $status) {
            throw new \DomainException("Transport request is already {$status->value}");
        }

        $this->status = $status;
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'type' => $this->type->value,
            'type_label' => $this->type->label(),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'fk_route' => $this->fk_route,
            'fk_vehicle' => $this->fk_vehicle,
            'fk_driver' => $this->fk_driver,
            'started_at' => $this->startedAt->format('Y-m-d H:i:s'),
            'completed_at' => $this->completedAt->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}