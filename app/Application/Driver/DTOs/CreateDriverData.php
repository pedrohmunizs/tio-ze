<?php

namespace App\Application\Driver\DTOs;

use Illuminate\Http\Request;

class CreateDriverData
{
    public function __construct(
        public readonly string $zip_code,
        public readonly string $street,
        public readonly string $number,
        public readonly string $neighborhood,
        public readonly string $city,
        public readonly string $state,
        public readonly int $fk_user,
        public readonly int $fk_provider,
        public readonly ?bool $is_autonomous = false,
        public readonly ?string $complement = null,
        public readonly ?float $latitude = null,
        public readonly ?float $longitude = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            is_autonomous: $request->input('is_autonomous'),
            zip_code: $request->input('zip_code'),
            street: $request->input('street'),
            number: $request->input('number'),
            complement: $request->input('complement'),
            neighborhood: $request->input('neighborhood'),
            city: $request->input('city'),
            state: $request->input('state'),
            latitude: $request->input('latitude') ? (float) $request->input('latitude') : null,
            longitude: $request->input('longitude') ? (float) $request->input('longitude') : null,
            fk_user: $request->input('fk_user'),
            fk_provider: $request->input('fk_provider'),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function validate(): array
    {
        return [
            'zip_code' => 'nullable|string|max:10',
            'street' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:20',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|size:2',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'fk_user' => 'required|numeric',
            'fk_provider' => 'required|numeric',
            'is_autonomous' => 'nullable|bool',
        ];
    }
}