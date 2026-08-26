<?php
// app/Domain/Address/Entities/Address.php

namespace App\Domain\Address\Entities;

use App\Domain\Address\ValueObjects\Coordinates;
use DateTimeImmutable;

class Address
{
    private ?int $id;
    private string $zipCode;
    private string $street;
    private string $number;
    private ?string $complement;
    private string $neighborhood;
    private string $city;
    private string $state;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt;
    private ?DateTimeImmutable $deletedAt;

    public function __construct(
        string $zipCode,
        string $street,
        string $number,
        string $neighborhood,
        string $city,
        string $state,
        ?string $complement = null,
        ?int $id = null,
    ) {
        $this->zipCode = $this->sanitizeZipCode($zipCode);
        $this->street = $street;
        $this->number = $number;
        $this->complement = $complement;
        $this->neighborhood = $neighborhood;
        $this->city = $city;
        $this->state = $this->sanitizeState($state);
        $this->id = $id;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = null;
        $this->deletedAt = null;

        $this->validate();
    }

    /**
     * Validações do endereço
     */
    private function validate(): void
    {
        if (empty($this->zipCode)) {
            throw new \InvalidArgumentException('ZIP code cannot be empty');
        }

        if (empty($this->street)) {
            throw new \InvalidArgumentException('Street cannot be empty');
        }

        if (empty($this->number)) {
            throw new \InvalidArgumentException('Number cannot be empty');
        }

        if (empty($this->neighborhood)) {
            throw new \InvalidArgumentException('Neighborhood cannot be empty');
        }

        if (empty($this->city)) {
            throw new \InvalidArgumentException('City cannot be empty');
        }

        if (empty($this->state) || strlen($this->state) !== 2) {
            throw new \InvalidArgumentException('State must be 2 characters');
        }

        if (!preg_match('/^\d{5}-?\d{3}$/', $this->zipCode)) {
            throw new \InvalidArgumentException('Invalid ZIP code format. Use: 12345-678 or 12345678');
        }
    }

    /**
     * Sanitiza o CEP (remove formatação)
     */
    private function sanitizeZipCode(string $zipCode): string
    {
        return preg_replace('/[^0-9]/', '', $zipCode);
    }

    /**
     * Sanitiza o estado (maiúsculo e apenas 2 caracteres)
     */
    private function sanitizeState(string $state): string
    {
        return strtoupper(substr($state, 0, 2));
    }

    /**
     * Métodos de negócio
     */
    public function update(
        ?string $zipCode = null,
        ?string $street = null,
        ?string $number = null,
        ?string $complement = null,
        ?string $neighborhood = null,
        ?string $city = null,
        ?string $state = null,
    ): self {
        if ($zipCode !== null) {
            $this->zipCode = $this->sanitizeZipCode($zipCode);
        }

        if ($street !== null) {
            $this->street = $street;
        }

        if ($number !== null) {
            $this->number = $number;
        }

        if ($complement !== null) {
            $this->complement = $complement;
        }

        if ($neighborhood !== null) {
            $this->neighborhood = $neighborhood;
        }

        if ($city !== null) {
            $this->city = $city;
        }

        if ($state !== null) {
            $this->state = $this->sanitizeState($state);
        }

        $this->updatedAt = new DateTimeImmutable();
        $this->validate();

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

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    /**
     * Getters
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function getFormattedZipCode(): string
    {
        return substr($this->zipCode, 0, 5) . '-' . substr($this->zipCode, 5);
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getComplement(): ?string
    {
        return $this->complement;
    }

    public function getNeighborhood(): string
    {
        return $this->neighborhood;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    /**
     * Setters (para uso interno em repositórios/mappers)
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function setDeletedAt(?DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    /**
     * Comparação
     */
    public function equals(Address $other): bool
    {
        return $this->zipCode === $other->getZipCode() &&
               $this->street === $other->getStreet() &&
               $this->number === $other->getNumber() &&
               $this->complement === $other->getComplement() &&
               $this->neighborhood === $other->getNeighborhood() &&
               $this->city === $other->getCity() &&
               $this->state === $other->getState();
    }

    /**
     * To Array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'zip_code' => $this->zipCode,
            'zip_code_formatted' => $this->getFormattedZipCode(),
            'street' => $this->street,
            'number' => $this->number,
            'complement' => $this->complement,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'state' => $this->state,
            // 'latitude' => $this->latitude,
            // 'longitude' => $this->longitude,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Factory methods
     */
    public static function create(
        string $zipCode,
        string $street,
        string $number,
        string $neighborhood,
        string $city,
        string $state,
        ?string $complement = null,
    ): self {
        return new self(
            zipCode: $zipCode,
            street: $street,
            number: $number,
            neighborhood: $neighborhood,
            city: $city,
            state: $state,
            complement: $complement,
        );
    }

    /**
     * Cria a partir de um array (útil para importação)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            zipCode: $data['zip_code'] ?? $data['zipCode'],
            street: $data['street'],
            number: $data['number'],
            neighborhood: $data['neighborhood'] ?? $data['bairro'],
            city: $data['city'],
            state: $data['state'],
            complement: $data['complement'] ?? null,
            id: $data['id'] ?? null,
        );
    }
}