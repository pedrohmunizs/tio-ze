<?php

namespace App\Domain\Driver\Entities;

use App\Domain\Driver\Enums\DriverStatus;
use DateTimeImmutable;

class Driver
{
    private ?int $id;
    private ?string $licenseNumber = null;
    private ?string $licenseCategory = null;
    private ?string $licenseValidUntil = null;
    private DriverStatus $status;
    private ?bool $isAutonomous;
    private int $userId;
    private int $providerId;
    private ?DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;
    private ?DateTimeImmutable $deletedAt = null;

    public function __construct(
        int $userId,
        int $providerId,
        bool $isAutonomous = false,
        DriverStatus $status = DriverStatus::PENDING,
        ?string $licenseNumber = null,
        ?string $licenseCategory = null,
        ?string $licenseValidUntil = null,
        ?int $id = null,
    )
    {
        $this->userId = $userId;
        $this->providerId = $providerId;
        $this->isAutonomous = $isAutonomous;
        $this->status = $status;
        $this->licenseNumber = $licenseNumber;
        $this->licenseCategory = $licenseCategory;
        $this->licenseValidUntil = $licenseValidUntil;
        $this->id = $id;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getLicenseNumber(): ?string { return $this->licenseNumber; }
    public function getLicenseCategory(): ?string { return $this->licenseCategory; }
    public function getLicenseValidUntil(): ?string { return $this->licenseValidUntil; }
    public function getStatus(): DriverStatus { return $this->status; }
    public function getIsAutonomous(): bool { return $this->isAutonomous; }
    public function getProviderId(): ?int { return $this->providerId; }
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
            'license_number' => $this->licenseNumber,
            'license_category' => $this->licenseCategory,
            'license_valid_until' => $this->licenseValidUntil,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'is_autonomous' => $this->isAutonomous,
            'fk_provider' => $this->providerId,
            'fk_user' => $this->userId,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}