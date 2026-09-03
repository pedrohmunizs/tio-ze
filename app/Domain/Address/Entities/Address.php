<?php

namespace App\Domain\Address\Entities;

use App\Domain\Address\Enums\GeocodeLocationType;
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
    private ?float $latitude;
    private ?float $longitude;
    private ?GeocodeLocationType $locationType;
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
        ?float $latitude = null,
        ?float $longitude = null,
        ?GeocodeLocationType $locationType = null,
        ?int $id = null,
    ) {
        $this->zipCode = $this->sanitizeZipCode($zipCode);
        $this->street = $street;
        $this->number = $number;
        $this->complement = $complement;
        $this->neighborhood = $neighborhood;
        $this->city = $city;
        $this->state = $this->sanitizeState($state);
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->locationType = $locationType;
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

        if ($this->latitude !== null && ($this->latitude < -90 || $this->latitude > 90)) {
            throw new \InvalidArgumentException('Latitude must be between -90 and 90');
        }

        if ($this->longitude !== null && ($this->longitude < -180 || $this->longitude > 180)) {
            throw new \InvalidArgumentException('Longitude must be between -180 and 180');
        }
    }

    private function sanitizeZipCode(string $zipCode): string
    {
        return preg_replace('/[^0-9]/', '', $zipCode);
    }

    private function sanitizeState(string $state): string
    {
        return strtoupper(substr($state, 0, 2));
    }

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

    public function updateCoordinates(?float $latitude, ?float $longitude, ?GeocodeLocationType $locationType): self
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->locationType = $locationType;
        $this->updatedAt = new DateTimeImmutable();
        
        return $this;
    }

    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
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

    public function getId(): ?int { return $this->id; }
    public function getZipCode(): string { return $this->zipCode;}
    public function getFormattedZipCode(): string { return substr($this->zipCode, 0, 5) . '-' . substr($this->zipCode, 5);}
    public function getStreet(): string { return $this->street;}
    public function getNumber(): string { return $this->number;}
    public function getComplement(): ?string { return $this->complement;}
    public function getNeighborhood(): string { return $this->neighborhood;}
    public function getCity(): string { return $this->city;}
    public function getState(): string { return $this->state;}
    public function getLatitude(): ?float { return $this->latitude;}
    public function getLongitude(): ?float { return $this->longitude;}
    public function getLocationType(): ?GeocodeLocationType { return $this->locationType;}
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt;}
    public function getUpdatedAt(): ?DateTimeImmutable { return $this->updatedAt;}
    public function getDeletedAt(): ?DateTimeImmutable { return $this->deletedAt;}

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
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'location_type' => $this->locationType,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public static function create(
        string $zipCode,
        string $street,
        string $number,
        string $neighborhood,
        string $city,
        string $state,
        ?string $complement = null,
        ?float $latitude = null,
        ?float $longitude = null,
        ?GeocodeLocationType $locationType = null,
    ): self {
        return new self(
            zipCode: $zipCode,
            street: $street,
            number: $number,
            neighborhood: $neighborhood,
            city: $city,
            state: $state,
            complement: $complement,
            latitude: $latitude,
            longitude: $longitude,
            locationType: $locationType
        );
    }

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
            latitude: isset($data['latitude']) ? (float) $data['latitude'] : null,
            longitude: isset($data['longitude']) ? (float) $data['longitude'] : null,
            locationType: $data['location_type'],
            id: $data['id'] ?? null,
        );
    }

    public static function fromGeocoding(array $geocodeData): self
    {
        return new self(
            zipCode: $geocodeData['zip_code'] ?? $geocodeData['cep'] ?? '',
            street: $geocodeData['street'] ?? $geocodeData['logradouro'] ?? '',
            number: $geocodeData['number'] ?? 's/n',
            neighborhood: $geocodeData['neighborhood'] ?? $geocodeData['bairro'] ?? '',
            city: $geocodeData['city'] ?? $geocodeData['localidade'] ?? '',
            state: $geocodeData['state'] ?? $geocodeData['uf'] ?? '',
            complement: $geocodeData['complement'] ?? $geocodeData['complemento'] ?? null,
            latitude: isset($geocodeData['latitude']) ? (float) $geocodeData['latitude'] : null,
            longitude: isset($geocodeData['longitude']) ? (float) $geocodeData['longitude'] : null,
        );
    }
}