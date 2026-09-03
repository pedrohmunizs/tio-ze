<?php

namespace App\Domain\Address\Services;

use App\Domain\Address\Entities\Address;
use App\Domain\Address\Enums\GeocodeLocationType;
use App\Domain\Address\Repositories\AddressRepositoryInterface;
use App\Helpers\AddressHelper;

class AddressService
{
    public function __construct(
        private AddressRepositoryInterface $addressRepository,
    ) {}

    public function createAddressFromData(
        string $zipCode, 
        string $street, 
        string $number, 
        string $neighborhood, 
        string $city, 
        string $state,
        ?string $complement = null
    ): ?int 
    {
        if (!$zipCode) {
            return null;
        }

        $geocodeData = AddressHelper::getAddressWithCoordinates($zipCode, $number);

        $address = new Address(
            zipCode: $zipCode,
            street: $street ?? $geocodeData['street'] ?? null,
            number: $number,
            complement: $complement,
            neighborhood: $neighborhood ?? $geocodeData['neighborhood'] ?? null,
            city: $city ?? $geocodeData['city'] ?? null,
            state: $state ?? $geocodeData['state'] ?? null,
            latitude: $geocodeData['latitude'] ?? null,
            longitude: $geocodeData['longitude'] ?? null,
            locationType: GeocodeLocationType::fromString($geocodeData['location_type'] ?? null),
        );

        return $this->addressRepository->save($address);
    }
}