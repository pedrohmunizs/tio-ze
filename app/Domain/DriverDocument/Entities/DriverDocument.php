<?php

namespace App\Domain\DriverDocument\Entities;

use App\Domain\Driver\Entities\Driver;
use App\Domain\DriverDocument\Enums\DriverDocumentStatus;
use App\Domain\DriverDocument\Enums\DriverDocumentType;
use DateTimeImmutable;

class DriverDocument
{
    private ?int $id;
    private int $fk_driver;
    private string $file_path;
    private string $valid_until;
    private DriverDocumentType $type;
    private DriverDocumentStatus $status;
    private ?Driver $driver = null;
    private ?DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(
        int $fk_driver,
        string $file_path,
        string $valid_until,
        DriverDocumentType $type,
        DriverDocumentStatus $status = DriverDocumentStatus::PENDING,
        ?int $id = null
    )
    {
        $this->fk_driver = $fk_driver;
        $this->file_path = $file_path;
        $this->valid_until = $valid_until;
        $this->type = $type;
        $this->status = $status;
        $this->id = $id;
        $this->createdAt = new DateTimeImmutable();
    }

    public function loadDriver(Driver $driver): self
    {
        $this->driver = $driver;
        return $this;
    }

    public function getId(): ?int { return $this->id; }
    public function getDriverId(): int { return $this->fk_driver; }
    public function getFilePath(): string { return $this->file_path; }
    public function getValidUntil(): string { return $this->valid_until; }
    public function getStatus(): DriverDocumentStatus { return $this->status; }
    public function getType(): DriverDocumentType { return $this->type; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?DateTimeImmutable { return $this->updatedAt; }
    public function getDriver(): ?Driver { return $this->driver; }
    public function isPending(): bool { return $this->status === DriverDocumentStatus::PENDING; }
    public function isApproved(): bool { return $this->status === DriverDocumentStatus::APPROVED; }
    public function isRejected(): bool { return $this->status === DriverDocumentStatus::REJECTED; }
    
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
        $status = DriverDocumentStatus::fromString($status);

        if ($this->status === $status) {
            throw new \DomainException("Driver document is already {$status->value}");
        }

        $this->status = $status;
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'file_path' => $this->file_path,
            'valid_until' => $this->valid_until,
            'type' => $this->type->value,
            'type_label' => $this->type->label(),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'fk_driver' => $this->fk_driver,
            'driver' => $this->driver?->toArray(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}