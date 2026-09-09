<?php

namespace App\Domain\School\Entities;

use App\Domain\Address\Entities\Address;
use App\Domain\School\Enums\SchoolStatus;
use DateTimeImmutable;

class School
{
    private ?int $id;
    private string $name;
    private string $phone;
    private ?string $photo = null;
    private SchoolStatus $status;
    private int $addressId;
    private ?Address $address = null;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;
    private ?DateTimeImmutable $deletedAt = null;

    public function __construct(
        string $name,
        string $phone,
        int $addressId,
        ?string $photo = null,
        SchoolStatus $status = SchoolStatus::ACTIVE,
        ?int $id = null,
    )
    {
        $this->validate($name);
        $this->name = $name;
        $this->phone = $phone;
        $this->photo = $photo;
        $this->status = $status;
        $this->addressId = $addressId;
        $this->id = $id;
        $this->createdAt = new DateTimeImmutable();
    }

    private function validate(string $name): void
    {
        if (empty(trim($name))) {
            throw new \InvalidArgumentException('Child name cannot be empty');
        }
    }

    public function update(?string $name = null, ?string $phone = null, ?int $addressId = null): self
    {
        if ($name !== null) {
            if (empty(trim($name))) {
                throw new \InvalidArgumentException('Child name cannot be empty');
            }
            $this->name = $name;
        }

        if ($phone !== null) {
            $this->phone = $phone;
        }

        if ($addressId !== null) {
            $this->addressId = $addressId;
        }

        $this->updatedAt = new DateTimeImmutable();
        return $this;
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
        return $this->status === SchoolStatus::ACTIVE;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function activate(): void
    {
        if ($this->status === SchoolStatus::ACTIVE) {
            throw new \DomainException('Child is already active');
        }
        $this->status = SchoolStatus::ACTIVE;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function inactivate(): void
    {
        if ($this->status === SchoolStatus::INACTIVE) {
            throw new \DomainException('Child is already blocked');
        }
        $this->status = SchoolStatus::INACTIVE;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function loadAddress(Address $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getPhone(): string { return $this->phone; }
    public function getStatus(): SchoolStatus { return $this->status; }
    public function getAddressId(): ?int { return $this->addressId; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?DateTimeImmutable { return $this->updatedAt; }
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

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
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
            'phone' => $this->phone,
            'photo' => $this->photo,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'fk_address' => $this->addressId,
            'address' => $this->address?->toArray(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}