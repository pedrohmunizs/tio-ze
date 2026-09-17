<?php

namespace App\Application\Route\DTOs;

use Illuminate\Http\Request;

class ChangeVehicleRouteData
{
    public function __construct(
        public readonly int $fk_vehicle,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $request->validate(self::rules(), self::messages());

        return new self(
            fk_vehicle: (int) $request->input('fk_vehicle'),
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