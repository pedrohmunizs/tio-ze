<?php

namespace App\Application\Driver\DTOs;

use Carbon\Carbon;
use Illuminate\Http\Request;

class UpdateDriverData
{
    public function __construct(
        public readonly string $license_number,
        public readonly string $license_category,
        public readonly Carbon $license_valid_until,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $request->validate(self::rules(), self::messages());

        return new self(
            license_number: $request->input('license_number'),
            license_category: $request->input('license_category'),
            license_valid_until: Carbon::createFromFormat('d/m/Y', $request->input('license_valid_until')),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function rules(): array
    {
        return [
            'license_number' => 'nullable|string|max:255',
            'license_category' => 'nullable|string|max:255',
            'license_valid_until' => 'required|date_format:d/m/Y',
        ];
    }

    public static function messages(): array
    {
        return [
            'id.required' => 'O ID é obrigatório',
            'id.exists' => 'Registro não encontrado',
        ];
    }
}