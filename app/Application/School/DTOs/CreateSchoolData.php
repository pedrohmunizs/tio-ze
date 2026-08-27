<?php

namespace App\Application\School\DTOs;

use Illuminate\Http\Request;

class CreateSchoolData
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $phone = null,
        public readonly ?string $photo = null,
        public readonly string $zip_code,
        public readonly string $street,
        public readonly string $number,
        public readonly ?string $complement = null,
        public readonly string $neighborhood,
        public readonly string $city,
        public readonly string $state,
        public readonly ?float $latitude = null,
        public readonly ?float $longitude = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            phone: $request->input('phone'),
            photo: $request->input('photo'),
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
        return get_object_vars($this);
    }

    public function validate(): array
    {
        return [
            'name' => 'required|string|max:255',
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
}