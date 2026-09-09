<?php

namespace App\Domain\Child\Entities;

use App\Domain\Address\Entities\Address;
use App\Domain\Address\Repositories\AddressRepositoryInterface;
use App\Domain\Child\Enums\ChildStatus;
use DateTimeImmutable;
use Illuminate\Support\Facades\App;

class Child
{
    private ?int $id;
    private string $name;
    private ?string $phone;
    private string $grade;
    private ChildStatus $status;
    private ?int $addressId;
    private ?int $parentId;
    private ?int $schoolId;
    private ?Address $address = null;
    private ?DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;
    private ?DateTimeImmutable $deletedAt = null;

    public function __construct(
        string $name,
        ?string $phone = null,
        string $grade,
        ChildStatus $status = ChildStatus::ACTIVE,
        ?int $addressId = null,
        ?int $parentId = null,
        ?int $schoolId = null,
        ?int $id = null,
    )
    {
        $this->validate($name, $grade);
        $this->id = $id;
        $this->name = $name;
        $this->phone = $phone;
        $this->grade = $grade;
        $this->status = $status;
        $this->addressId = $addressId;
        $this->parentId = $parentId;
        $this->schoolId = $schoolId;
        $this->createdAt = new DateTimeImmutable();
    }

    private function validate(string $name, string $grade): void
    {
        if (empty(trim($name))) {
            throw new \InvalidArgumentException('Child name cannot be empty');
        }

        if (empty(trim($grade))) {
            throw new \InvalidArgumentException('Child grade cannot be empty');
        }
    }

    public function update(
        ?string $name = null,
        ?string $phone = null,
        ?string $grade = null,
        ?int $addressId = null,
        ?int $schoolId = null,
    ): self {
        if ($name !== null) {
            if (empty(trim($name))) {
                throw new \InvalidArgumentException('Child name cannot be empty');
            }
            $this->name = $name;
        }

        if ($phone !== null) {
            $this->phone = $phone;
        }

        if ($grade !== null) {
            if (empty(trim($grade))) {
                throw new \InvalidArgumentException('Child grade cannot be empty');
            }
            $this->grade = $grade;
        }

        if ($addressId !== null) {
            $this->addressId = $addressId;
        }

        if ($schoolId !== null) {
            $this->schoolId = $schoolId;
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
        return $this->status === ChildStatus::ACTIVE;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function activate(): void
    {
        if ($this->status === ChildStatus::ACTIVE) {
            throw new \DomainException('Child is already active');
        }
        $this->status = ChildStatus::ACTIVE;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function inactivate(): void
    {
        if ($this->status === ChildStatus::INACTIVE) {
            throw new \DomainException('Child is already blocked');
        }
        $this->status = ChildStatus::INACTIVE;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getGrade(): string { return $this->grade; }
    public function getPhone(): string { return $this->phone; }
    public function getStatus(): ChildStatus { return $this->status; }
    public function getAddressId(): ?int { return $this->addressId; }
    public function getSchoolId(): ?int { return $this->schoolId; }
    public function getParentId(): ?int { return $this->parentId; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?DateTimeImmutable { return $this->updatedAt; }

    public function getAddress(): ?Address
    {
        if ($this->address === null && $this->addressId) {
            $repository = App::make(AddressRepositoryInterface::class);
            $this->address = $repository->findById($this->addressId);
        }
        
        return $this->address;
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
            'grade' => $this->grade,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'address_id' => $this->addressId,
            'parent_id' => $this->parentId,
            'school_id' => $this->schoolId,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}