<?php

namespace App\Domain\Provider\Entities;

use App\Domain\Provider\Enums\ProviderStatus;
use DateTimeImmutable;

class Provider
{
    private ?int $id;
    private string $name;
    private string $phone;
    private ?string $description;
    private ProviderStatus $status;
    private float $rating;
    private bool $isAutonomous;
    private int $userId;
    private ?int $addressId = null;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;
    private ?DateTimeImmutable $deletedAt = null;

    public function __construct(
        string $name,
        string $phone,
        int $userId,
        int $addressId,
        bool $isAutonomous = false,
        ProviderStatus $status = ProviderStatus::PENDING,
        ?string $description = null,
        float $rating = 0.0,
        ?int $id = null,
    )
    {
        $this->name = $name;
        $this->phone = $phone;
        $this->userId = $userId;
        $this->addressId = $addressId;
        $this->description = $description;
        $this->status = $status;
        $this->rating = $rating;
        $this->isAutonomous = $isAutonomous;
        $this->id = $id;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = null;
        $this->deletedAt = null;
    }

    public function update(
        ?string $name = null, 
        ?string $phone = null, 
        ?string $description = null
    ): self 
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

        if ($description !== null) {
            $this->description = $description;
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
        return $this->status === ProviderStatus::ACTIVE;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function activate(): void
    {
        if ($this->status === ProviderStatus::ACTIVE) {
            throw new \DomainException('Provider is already active');
        }
        $this->status = ProviderStatus::ACTIVE;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function inactivate(): void
    {
        if ($this->status === ProviderStatus::INACTIVE) {
            throw new \DomainException('Provider is already inactivate');
        }
        $this->status = ProviderStatus::INACTIVE;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getPhone(): string { return $this->phone; }
    public function getDescription(): ?string { return $this->description; }
    public function getStatus(): ProviderStatus { return $this->status; }
    public function getRating(): float { return $this->rating; }
    public function getIsAutonomous(): bool { return $this->isAutonomous; }
    public function getAddressId(): ?int { return $this->addressId; }
    public function getUserId(): ?int { return $this->userId; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?DateTimeImmutable { return $this->updatedAt; }

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
            'description' => $this->description,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'rating' => $this->rating,
            'is_autonomous' => $this->isAutonomous,
            'fk_address' => $this->addressId,
            'fk_user' => $this->userId,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}