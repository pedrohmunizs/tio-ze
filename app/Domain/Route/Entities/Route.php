<?php
// app/Domain/Route/Entities/Route.php

namespace App\Domain\Route\Entities;

use App\Domain\Driver\Entities\Driver;
use App\Domain\Driver\Repositories\DriverRepositoryInterface;
use App\Domain\Provider\Entities\Provider;
use App\Domain\Route\Enums\RouteStatus;
use App\Domain\Route\ValueObjects\DaysOfWeek;
use App\Domain\Route\ValueObjects\Time;
use App\Domain\Route\ValueObjects\Price;
use App\Domain\School\Entities\School;
use App\Domain\School\Repositories\SchoolRepositoryInterface;
use App\Domain\Vehicle\Entities\Vehicle;
use DateTimeImmutable;
use Illuminate\Support\Facades\App;

class Route
{
    private ?int $id;
    private string $name;
    private Price $price;
    private Time $goingTime;
    private Time $returningTime;
    private DaysOfWeek $daysOfWeek;
    private int $schoolId;
    private int $providerId;
    private int $driverId;
    private int $vehicleId;
    private RouteStatus $status;
    private ?Driver $driver = null;
    private ?School $school = null;
    private ?Provider $provider = null;
    private ?Vehicle $vehicle = null;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt;
    private ?DateTimeImmutable $deletedAt;

    public function __construct(
        string $name,
        Price $price,
        Time $goingTime,
        Time $returningTime,
        DaysOfWeek $daysOfWeek,
        int $schoolId,
        int $providerId,
        int $driverId,
        int $vehicleId,
        RouteStatus $status = RouteStatus::ACTIVE,
        ?int $id = null,
    ) {
        $this->validate($goingTime, $returningTime);
        
        $this->name = $name;
        $this->price = $price;
        $this->goingTime = $goingTime;
        $this->returningTime = $returningTime;
        $this->daysOfWeek = $daysOfWeek;
        $this->schoolId = $schoolId;
        $this->providerId = $providerId;
        $this->driverId = $driverId;
        $this->vehicleId = $vehicleId;
        $this->status = $status;
        $this->id = $id;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = null;
        $this->deletedAt = null;
    }

    private function validate(Time $goingTime, Time $returningTime): void
    {
        if ($returningTime->isBefore($goingTime)) {
            throw new \InvalidArgumentException('Returning time must be after going time');
        }

        if ($returningTime->equals($goingTime)) {
            throw new \InvalidArgumentException('Returning time cannot be equal to going time');
        }
    }

    public function update(
        ?string $name = null,
        ?Price $price = null,
        ?Time $goingTime = null,
        ?Time $returningTime = null,
        ?DaysOfWeek $daysOfWeek = null,
        ?int $schoolId = null,
        ?int $providerId = null,
    ): self {
        if ($name !== null) {
            if (empty(trim($name))) {
                throw new \InvalidArgumentException('Route name cannot be empty');
            }
            $this->name = $name;
        }

        if ($price !== null) {
            $this->price = $price;
        }

        if ($goingTime !== null && $returningTime !== null) {
            $this->validate($goingTime, $returningTime);
            $this->goingTime = $goingTime;
            $this->returningTime = $returningTime;
        } elseif ($goingTime !== null) {
            $this->validate($goingTime, $this->returningTime);
            $this->goingTime = $goingTime;
        } elseif ($returningTime !== null) {
            $this->validate($this->goingTime, $returningTime);
            $this->returningTime = $returningTime;
        }

        if ($daysOfWeek !== null) {
            $this->daysOfWeek = $daysOfWeek;
        }

        if ($schoolId !== null) {
            $this->schoolId = $schoolId;
        }

        if ($providerId !== null) {
            $this->providerId = $providerId;
        }

        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function activate(): void
    {
        if ($this->status === RouteStatus::ACTIVE) {
            throw new \DomainException('Route is already active');
        }
        $this->status = RouteStatus::ACTIVE;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function inactivate(): void
    {
        if ($this->status === RouteStatus::INACTIVE) {
            throw new \DomainException('Route is already inactive');
        }
        $this->status = RouteStatus::INACTIVE;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function delete(): self
    {
        $this->deletedAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function restore(): self
    {
        $this->deletedAt = null;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function isActive(): bool
    {
        return $this->status === RouteStatus::ACTIVE;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function loadSchool(School $school): self
    {
        $this->school = $school;
        return $this;
    }

    public function loadProvider(Provider $provider): self
    {
        $this->provider = $provider;
        return $this;
    }

    public function loadDriver(Driver $driver): self
    {
        $this->driver = $driver;
        return $this;
    }

    public function loadVehicle(Vehicle $vehicle): self
    {
        $this->vehicle = $vehicle;
        return $this;
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getPrice(): Price { return $this->price; }
    public function getPriceValue(): float { return $this->price->getValue(); }
    public function getGoingTime(): Time { return $this->goingTime; }
    public function getGoingTimeValue(): string { return $this->goingTime->getValue(); }
    public function getReturningTime(): Time { return $this->returningTime; }
    public function getReturningTimeValue(): string { return $this->returningTime->getValue(); }
    public function getDaysOfWeek(): DaysOfWeek { return $this->daysOfWeek; }
    public function getDaysOfWeekValue(): string { return $this->daysOfWeek->toString(); }
    public function getSchoolId(): int { return $this->schoolId; }
    public function getProviderId(): int { return $this->providerId; }
    public function getDriverId(): int { return $this->driverId; }
    public function getVehicleId(): int { return $this->vehicleId; }
    public function getStatus(): RouteStatus { return $this->status; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?DateTimeImmutable { return $this->updatedAt; }
    public function getDeletedAt(): ?DateTimeImmutable { return $this->deletedAt; }
    public function getSchool(): ?School { return $this->school; }
    public function getDriver(): ?Driver { return $this->driver; }
    public function getProvider(): ?Provider { return $this->provider; }
    public function getVehicle(): ?Vehicle { return $this->vehicle; }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function setDeletedAt(?DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price->getValue(),
            'price_formatted' => $this->price->getFormatted(),
            'going_time' => $this->goingTime->getValue(),
            'returning_time' => $this->returningTime->getValue(),
            'days_of_week' => $this->daysOfWeek->toString(),
            'days_of_week_array' => $this->daysOfWeek->toArray(),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'school_id' => $this->schoolId,
            'school' => $this->school?->toArray(),
            'provider' => $this->provider?->toArray(),
            'driver' => $this->driver?->toArray(),
            'provider_id' => $this->providerId,
            'driver_id' => $this->driverId,
            'vehicle_id' => $this->vehicleId,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}