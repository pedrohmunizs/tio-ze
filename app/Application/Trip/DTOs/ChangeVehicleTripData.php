<?php

namespace App\Application\Trip\DTOs;

use Illuminate\Http\Request;

class ChangeVehicleTripData
{
    public function __construct(
        public readonly int $fk_vehicle,
    ) {}

    public static function fromRequest(int $fk_vehicle): self
    {
        return new self(
            fk_vehicle: (int) $fk_vehicle,
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function rules(): array
    {
        return [
            'fk_vehicle' => 'required|exists:vehicles,id',
        ];
    }

    public static function messages(): array
    {
        return [
            'fk_vehicle.required' => 'O veículo é obrigatório',
            'fk_vehicle.exists' => 'Veículo não encontrado',
        ];
    }
}