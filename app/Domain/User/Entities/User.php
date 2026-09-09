<?php

namespace App\Domain\User\Entities;

use App\Domain\Address\Entities\Address;
use App\Domain\Address\Repositories\AddressRepositoryInterface;
use App\Domain\User\Enums\UserStatus;
use App\Domain\User\ValueObjects\CPF;
use App\Domain\User\ValueObjects\Email;
use DateTimeImmutable;
use Illuminate\Support\Facades\App;

class User
{
    private ?int $id;
    private string $name;
    private Email $email;
    private CPF $cpf;
    private string $phone;
    private string $password;
    private UserStatus $status;
    private ?int $addressId;
    private ?Address $address = null;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;
    private ?DateTimeImmutable $deletedAt = null;

    public function __construct(
        string $name, 
        Email $email, 
        Cpf $cpf, 
        string $phone, 
        string $password, 
        ?int $addressId = null, 
        ?int $id = null
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->cpf = $cpf;
        $this->phone = $phone;
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        $this->addressId = $addressId;
        $this->id = $id;
        $this->status = UserStatus::PENDING;
        $this->createdAt = new DateTimeImmutable();
    }

    public function activate(): void
    {
        if ($this->status === UserStatus::ACTIVE) {
            throw new \DomainException('User is already active');
        }
        $this->status = UserStatus::ACTIVE;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function block(): void
    {
        if ($this->status === UserStatus::BLOCKED) {
            throw new \DomainException('User is already blocked');
        }
        $this->status = UserStatus::BLOCKED;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function updateProfile(string $name, string $phone, ?int $addressId = null): void
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Name cannot be empty');
        }
        
        $this->name = $name;
        $this->phone = $phone;
        $this->addressId = $addressId;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function changePassword(string $newPassword): void
    {
        $this->password = password_hash($newPassword, PASSWORD_BCRYPT);
        $this->updatedAt = new DateTimeImmutable();
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::ACTIVE;
    }

    public function isBlocked(): bool
    {
        return $this->status === UserStatus::BLOCKED;
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): Email { return $this->email; }
    public function getCpf(): Cpf { return $this->cpf; }
    public function getPhone(): string { return $this->phone; }
    public function getPasswordHash(): string { return $this->password; }
    public function getStatus(): UserStatus { return $this->status; }
    public function getAddressId(): ?int { return $this->addressId; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?DateTimeImmutable { return $this->updatedAt; }
    // public function getDeletedAt(): ?DateTimeImmutable { return $this->deletedAt; }

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
            'email' => $this->email->getValue(),
            'cpf' => $this->cpf->getFormatted(),
            'phone' => $this->phone,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'address_id' => $this->addressId,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}