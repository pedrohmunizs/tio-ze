<?php

namespace App\Domain\VehicleDocument\Entities;

use App\Domain\Vehicle\Entities\Vehicle;
use App\Domain\VehicleDocument\Enums\VehicleDocumentStatus;
use App\Domain\VehicleDocument\Enums\VehicleDocumentType;
use DateTimeImmutable;

class VehicleDocument
{
    private ?int $id;
    private int $fk_vehicle;
    private string $file_path;
    private VehicleDocumentType $type;
    private VehicleDocumentStatus $status;
    private ?Vehicle $vehicle = null;
    private ?DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(
        int $fk_vehicle,
        string $file_path,
        VehicleDocumentType $type,
        VehicleDocumentStatus $status = VehicleDocumentStatus::PENDING,
        ?int $id = null
    )
    {
        $this->fk_vehicle = $fk_vehicle;
        $this->file_path = $file_path;
        $this->type = $type;
        $this->status = $status;
        $this->id = $id;
        $this->createdAt = new DateTimeImmutable();
    }

    public function loadVehicle(Vehicle $vehicle): self
    {
        $this->vehicle = $vehicle;
        return $this;
    }

    public function getId(): ?int { return $this->id; }
    public function getVehicleId(): int { return $this->fk_vehicle; }
    public function getFilePath(): string { return $this->file_path; }
    public function getStatus(): VehicleDocumentStatus { return $this->status; }
    public function getType(): VehicleDocumentType { return $this->type; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?DateTimeImmutable { return $this->updatedAt; }
    public function getVehicle(): ?Vehicle { return $this->vehicle; }

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
            'file_path' => $this->file_path,
            'type' => $this->type->value,
            'type_label' => $this->type->label(),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'fk_vehicle' => $this->fk_vehicle,
            'vehicle' => $this->vehicle?->toArray(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}