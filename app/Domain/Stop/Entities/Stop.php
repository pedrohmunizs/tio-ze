<?php

namespace App\Domain\Stop\Entities;

use App\Domain\Address\Entities\Address;
use App\Domain\Child\Entities\Child;
use App\Domain\Stop\Enums\StopType;
use DateTimeImmutable;

class Stop
{
    private ?int $id;
    private int $fk_route;
    private int $fk_address;
    private int $stop_order;
    private StopType $type;
    private ?Child $student = null;
    private ?Address $address = null;
    private ?DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(
        int $fk_route,
        int $fk_address,
        int $stop_order,
        StopType $type = StopType::GOING,
        ?int $id = null
    )
    {
        $this->fk_route = $fk_route;
        $this->fk_address = $fk_address;
        $this->stop_order = $stop_order;
        $this->type = $type;
        $this->id = $id;
        $this->createdAt = new DateTimeImmutable();
    }

    public function loadStudent(Child $student): self
    {
        $this->student = $student;
        return $this;
    }

    public function loadAddress(Address $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getId(): ?int { return $this->id; }
    public function getAddressId(): int { return $this->fk_address; }
    public function getRouteId(): int { return $this->fk_route; }
    public function getStopOrder(): int { return $this->stop_order; }
    public function getType(): StopType { return $this->type; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?DateTimeImmutable { return $this->updatedAt; }
    public function getStudent(): ?Child { return $this->student; }
    public function getAddress(): ?Address { return $this->address; }

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
            'type' => $this->type->value,
            'type_label' => $this->type->label(),
            'fk_address' => $this->fk_address,
            'fk_route' => $this->fk_route,
            'stop_order' => $this->stop_order,
            'student' => $this->student?->toArray(),
            'address' => $this->address?->toArray(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}