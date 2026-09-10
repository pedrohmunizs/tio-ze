<?php

namespace App\Application\VehicleDocument\DTOs;

use Illuminate\Http\Request;

class RespondVehicleDocumentData
{
    public function __construct(
        public readonly string $status,
    ) {}

    public static function fromRequest(Request $request): self
    {
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
            'status' => 'required|in:approved,rejected',
        ];
    }

    public static function messages(): array
    {
        return [
            'status.required' => 'A resposta é obrigatória',
        ];
    }
}