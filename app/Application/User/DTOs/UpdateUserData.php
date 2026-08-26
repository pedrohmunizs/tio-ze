<?php

namespace App\Application\User\DTOs;

use Illuminate\Http\Request;

class UpdateUserData
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $phone = null,
        public readonly ?string $zip_code = null,
        public readonly ?string $street = null,
        public readonly ?string $number = null,
        public readonly ?string $complement = null,
        public readonly ?string $neighborhood = null,
        public readonly ?string $city = null,
        public readonly ?string $state = null,
        public readonly ?float $latitude = null,
        public readonly ?float $longitude = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            phone: $request->input('phone'),
            zip_code: $request->input('zip_code'),
            street: $request->input('street'),
            number: $request->input('number'),
            complement: $request->input('complement'),
            neighborhood: $request->input('neighborhood'),
            city: $request->input('city'),
            state: $request->input('state'),
            latitude: $request->input('latitude') ? (float) $request->input('latitude') : null,
            longitude: $request->input('longitude') ? (float) $request->input('longitude') : null,
        );
    }

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn($value) => $value !== null);
    }

    public function validate(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'zip_code' => 'nullable|string|max:10',
            'street' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:20',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|size:2',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ];
    }

    public function hasChanges(): bool
    {
        return count(array_filter($this->toArray())) > 0;
    }
}