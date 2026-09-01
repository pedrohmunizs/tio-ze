<?php

namespace App\Domain\Contract\Entities;

use App\Domain\Contract\Enums\ContractStatus;
use Carbon\Carbon;
use DateTimeImmutable;

class Contract
{
    private ?int $id;
    private int $fk_student;
    private int $fk_route;
    private int $fk_provider;
    private int $fk_transport_request;
    private Carbon $start_date;
    private ?Carbon $end_date;
    private string $type;
    private ContractStatus $status;
    private ?DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(
        int $fk_student,
        int $fk_route,
        int $fk_provider,
        int $fk_transport_request,
        ?Carbon $start_date = null,
        ?Carbon $end_date = null,
        ContractStatus $status = ContractStatus::PENDING,
        ?int $id = null,
    )
    {
        $this->fk_student = $fk_student;
        $this->fk_route = $fk_route;
        $this->fk_provider = $fk_provider;
        $this->fk_transport_request = $fk_transport_request;
        $this->start_date = $start_date ?? Carbon::now();
        $this->end_date = $end_date;
        $this->status = $status;
        $this->id = $id;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getStartDate(): Carbon { return $this->start_date; }
    public function getEndDate(): ?Carbon { return $this->end_date; }
    public function getStudentId(): int { return $this->fk_student; }
    public function getProviderId(): int { return $this->fk_provider; }
    public function getRouteId(): int { return $this->fk_route; }
    public function getTransportRequestId(): int { return $this->fk_transport_request; }
    public function getStatus(): ContractStatus { return $this->status; }
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
            'start_date' => $this->start_date->format('Y-m-d'),
            'end_date' => $this->end_date->format('Y-m-d'),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'fk_provider' => $this->fk_provider,
            'fk_student' => $this->fk_student,
            'fk_route' => $this->fk_route,
            'fk_transport_request' => $this->fk_transport_request,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}