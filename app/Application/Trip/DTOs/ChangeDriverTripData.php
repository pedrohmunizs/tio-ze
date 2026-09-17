<?php

namespace App\Application\Trip\DTOs;

use Illuminate\Http\Request;

class ChangeDriverTripData
{
    public function __construct(
        public readonly int $fk_driver,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $request->validate(self::rules(), self::messages());

        return new self(
            fk_driver: (int) $request->input('fk_driver'),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function rules(): array
    {
        return [
            'fk_driver' => 'required|exists:vehicles,id',
        ];
    }

    public static function messages(): array
    {
        return [
            'fk_driver.required' => 'O motorista é obrigatório',
            'fk_driver.exists' => 'Motorista não encontrado',
        ];
    }
}