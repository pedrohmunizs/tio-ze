<?php

namespace App\Domain\TransportRequest\Entities;

use App\Domain\Child\Entities\Child;
use App\Domain\Child\Repositories\ChildRepositoryInterface;
use App\Domain\Route\Entities\Route;
use App\Domain\Route\Repositories\RouteRepositoryInterface;
use App\Domain\TransportRequest\Enums\TransportRequestStatus;
use DateTimeImmutable;
use Illuminate\Support\Facades\App;

class TransportRequest
{
    private ?int $id;
    private int $fk_route;
    private int $fk_provider;
    private int $fk_student;
    private ?Child $student = null;
    private ?Route $route = null;
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

    public function getStudent(): ?Child
    {
        if ($this->student === null && $this->fk_student) {
            $repository = App::make(ChildRepositoryInterface::class);
            $this->student = $repository->findById($this->fk_student);
        }
        
        return $this->student;
    }

    public function getRoute(): ?Route
    {
        if ($this->route === null && $this->fk_route) {
            $repository = App::make(RouteRepositoryInterface::class);
            $this->route = $repository->findById($this->fk_route);
        }
        
        return $this->route;
    }

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

    public function isPending(): bool
    {
        return $this->status === TransportRequestStatus::PENDING;
    }

    public function isAccepted(): bool
    {
        return $this->status === TransportRequestStatus::ACCEPTED;
    }

    public function isRejected(): bool
    {
        return $this->status === TransportRequestStatus::REJECTED;
    }

    public function isCancelled(): bool
    {
        return $this->status === TransportRequestStatus::CANCELLED;
    }

    public function respond(string $status, ?string $message = null) : self
    {
        $this->setStatus($status);

        if ($message) {
            $this->message = $message;
        }

        return $this;
    }

    public function setStatus(string $status): void
    {
        $status = TransportRequestStatus::fromString($status);

        if ($this->status === $status) {
            throw new \DomainException("Transport request is already {$status->value}");
        }

        $this->status = $status;
        $this->updatedAt = new DateTimeImmutable();
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