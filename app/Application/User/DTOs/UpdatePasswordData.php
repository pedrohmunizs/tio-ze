<?php

namespace App\Application\User\DTOs;

use Illuminate\Http\Request;

class UpdatePasswordData
{
    public function __construct(
        public readonly string $current_password,
        public readonly string $new_password,
        public readonly string $new_password_confirmation,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            current_password: $request->input('current_password'),
            new_password: $request->input('new_password'),
            new_password_confirmation: $request->input('new_password_confirmation'),
        );
    }

    public function validate(): array
    {
        return [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string',
        ];
    }
}