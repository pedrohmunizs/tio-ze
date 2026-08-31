<?php

namespace App\Application\TransportRequest\DTOs;

use Illuminate\Http\Request;

class RespondTransportRequestData
{
    public function __construct(
        public readonly string $status,
        public readonly ?string $message,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            status: $request->input('status'),
            message: $request->input('message'),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function rules(): array
    {
        return [
            'status' => 'required|in:accepted,rejected',
            'message' => 'nullable|string',
        ];
    }

    public static function messages(): array
    {
        return [
            'status.required' => 'A resposta é obrigatória',
        ];
    }
}