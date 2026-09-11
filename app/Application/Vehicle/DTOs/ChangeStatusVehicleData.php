<?php

namespace App\Application\Vehicle\DTOs;

use Illuminate\Http\Request;

class ChangeStatusVehicleData
{
    public function __construct(public readonly string $status) {}

    public static function fromRequest(Request $request): self
    {
        $request->validate(self::rules(), self::messages());

        return new self(
            status: $request->input('status'),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function rules(): array
    {
        return [
            'status' => 'required|in:active,inactive,maintenance',
        ];
    }

    public static function messages(): array
    {
        return [
            'status.required' => 'A resposta é obrigatória',
        ];
    }
}