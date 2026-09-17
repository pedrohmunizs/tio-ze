<?php

namespace App\Domain\TripStudent\Entities;

use App\Domain\Child\Entities\Child;
use App\Domain\Trip\Entities\Trip;
use App\Domain\TripStudent\Enums\TripStudentStatus;
use Carbon\Carbon;
use DateTimeImmutable;

class TripStudent
{
    private int $fk_trip;
    private int $fk_student;
    private ?Carbon $pickup_time = null;
    private ?Carbon $dropoff_time = null;
    private ?TripStudentStatus $status;
    private ?Trip $trip = null;
    private ?Child $student = null;
    private ?int $id;
    private ?DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(
        int $fk_trip,
        int $fk_student,
        ?Carbon $pickup_time = null,
        ?Carbon $dropoff_time = null,
        TripStudentStatus $status = TripStudentStatus::PENDING,
        ?int $id = null
    )
    {
        $this->fk_trip = $fk_trip;
        $this->fk_student = $fk_student;
        $this->pickup_time = $pickup_time;
        $this->dropoff_time = $dropoff_time;
        $this->status = $status;
        $this->id = $id;
        $this->createdAt = new DateTimeImmutable();
    }

    public function loadTrip(Trip $trip): self
    {
        $this->trip = $trip;
        return $this;
    }

    public function loadStudent(Child $student): self
    {
        $this->student = $student;
        return $this;
    }

    public function getId(): ?int { return $this->id; }
    public function getTripId(): ?int { return $this->fk_trip; }
    public function getStudentId(): ?int { return $this->fk_student; }
    public function getPickupTime(): ?Carbon { return $this->pickup_time; }
    public function getDropOffTime(): ?Carbon { return $this->dropoff_time; }
    public function getStatus(): ?TripStudentStatus { return $this->status; }
    public function getTrip(): ?Trip { return $this->trip; }
    public function getStudent(): ?Child { return $this->student; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): DateTimeImmutable { return $this->updatedAt; }

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

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'fk_trip' => $this->fk_trip,
            'fk_student' => $this->fk_student,
            'pickup_time' => $this->pickup_time->format('d/m/Y'),
            'dropoff_time' => $this->dropoff_time->format('d/m/Y'),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}