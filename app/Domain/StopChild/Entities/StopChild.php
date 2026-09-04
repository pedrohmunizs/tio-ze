<?php

namespace App\Domain\StopChild\Entities;

use DateTimeImmutable;

class StopChild
{
    private ?int $id;
    private int $fk_stop;
    private int $fk_child;
    private int $stop_order;
    private ?DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(
        int $fk_stop,
        int $fk_child,
        int $stop_order,
        ?int $id = null
    )
    {
        $this->fk_stop = $fk_stop;
        $this->fk_child = $fk_child;
        $this->stop_order = $stop_order;
        $this->id = $id;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getStopId(): int { return $this->fk_stop; }
    public function getChildId(): int { return $this->fk_child; }
    public function getStopOrder(): int { return $this->stop_order; }
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
            'fk_stop' => $this->fk_stop,
            'fk_child' => $this->fk_child,
            'stop_order' => $this->stop_order,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}