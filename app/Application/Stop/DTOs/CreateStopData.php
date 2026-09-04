<?php

namespace App\Application\Stop\DTOs;

use Illuminate\Http\Request;

class CreateStopData
{
    public function __construct(
        public readonly ?int $id = null,
        // Adicione as propriedades do DTO
        // Exemplo: public readonly string $name,
        // public readonly string $email,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            id: $request->input('id'),
            // Mapeie os campos do request
            // name: $request->input('name'),
            // email: $request->input('email'),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function validate(): array
    {
        return [
            // Regras de validação
            // 'name' => 'required|string|max:255',
            // 'email' => 'required|email|unique:users,email',
        ];
    }
}