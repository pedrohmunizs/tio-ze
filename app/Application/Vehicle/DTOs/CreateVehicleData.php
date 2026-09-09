<?php

namespace App\Application\Vehicle\DTOs;

use Illuminate\Http\Request;

class CreateVehicleData
{
    public function __construct(
        public readonly string $brand,
        public readonly string $model,
        public readonly string $plate,
        public readonly int $year,
        public readonly int $capacity,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            brand: $request->input('brand'),
            model: $request->input('model'),
            plate: $request->input('plate'),
            year: $request->input('year'),
            capacity: $request->input('capacity'),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function rules(): array
    {
        return [
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'plate' => 'required|string|max:255',
            'year' => 'required|numeric',
            'capacity' => 'required|numeric',
        ];
    }

    public static function messages(): array
    {
        return [
            'brand.required' => 'A marca do veículo é obrigatório',
            'model.required' => 'O modelo do veículo é obrigatório',
            'plate.required' => 'A placa do veículo é obrigatório',
            'year.required' => 'O ano do veículo é obrigatório',
            'capacity.required' => 'A capacidade do veículo é obrigatório',
        ];
    }
}