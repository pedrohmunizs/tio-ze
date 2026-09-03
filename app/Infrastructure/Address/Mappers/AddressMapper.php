<?php

namespace App\Infrastructure\Address\Mappers;

use App\Domain\Address\Entities\Address;
use App\Domain\Address\Enums\GeocodeLocationType;
use App\Infrastructure\Address\Models\AddressModel;
use DateTimeImmutable;

class AddressMapper
{
    public static function toDomain(AddressModel $model): Address
    {
        $locationType = GeocodeLocationType::fromString($model->location_type);

        $address = new Address(
            zipCode: $model->zip_code,
            street: $model->street,
            number: $model->number,
            neighborhood: $model->neighborhood,
            city: $model->city,
            complement: $model->complement,
            latitude: $model->latitude ? (float) $model->latitude : null,
            longitude: $model->longitude ? (float) $model->longitude : null,
            state: $model->state,
            locationType: $locationType,
        );

        $reflection = new \ReflectionClass($address);

        if ($model->created_at) {
            $createdAtProperty = $reflection->getProperty('createdAt');
            $createdAtProperty->setAccessible(true);
            $createdAtProperty->setValue($address, new DateTimeImmutable($model->created_at));
        }

        if ($model->updated_at) {
            $updatedAtProperty = $reflection->getProperty('updatedAt');
            $updatedAtProperty->setAccessible(true);
            $updatedAtProperty->setValue($address, new DateTimeImmutable($model->updated_at));
        }

        if ($model->deleted_at) {
            $deletedAtProperty = $reflection->getProperty('deletedAt');
            $deletedAtProperty->setAccessible(true);
            $deletedAtProperty->setValue($address, new DateTimeImmutable($model->deleted_at));
        }

        return $address;
    }

    public static function toArray(Address $address): array
    {
        return [
            'id' => $address->getId(),
            'zip_code' => $address->getzipCode(),
            'street' => $address->getStreet(),
            'number' => $address->getNumber(),
            'neighborhood' => $address->getNeighborhood(),
            'city' => $address->getCity(),
            'state' => $address->getState(),
            'complement' => $address->getComplement(),
            'latitude' => $address->getLatitude(),
            'longitude' => $address->getLongitude(),
            'location_type' => $address->getLocationType()?->value,
        ];
    }

    public static function toModel(Address $address): array
    {
        return self::toArray($address);
    }
}