<?php

namespace App\Domain\TransportRequest\Entities;

use App\Domain\TransportRequest\Enums\TransportRequestStatus;
use DateTimeImmutable;

class TransportRequest
{
    private ?int $id;
    private int $fk_route;
    private int $fk_provider;
    private int $fk_student;
    private ?string $message = null;
    private TransportRequestStatus $status;
    private ?DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(
        int $fk_route,
        int $fk_provider,
        int $fk_student,
        ?string $message = null,
        TransportRequestStatus $status = TransportRequestStatus::PENDING,
        ?int $id = null
    )
    {
        $this->fk_route = $fk_route;
        $this->fk_provider = $fk_provider;
        $this->fk_student = $fk_student;
        $this->message = $message;
        $this->status = $status;
        $this->id = $id;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getMessage(): ?string { return $this->message; }
    public function getStatus(): TransportRequestStatus { return $this->status; }
    public function getStudentId(): int { return $this->fk_student; }
    public function getProviderId(): int { return $this->fk_provider; }
    public function getRouteId(): int { return $this->fk_route; }
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

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'fk_student' => $this->fk_student,
            'fk_provider' => $this->fk_provider,
            'fk_route' => $this->fk_route,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}