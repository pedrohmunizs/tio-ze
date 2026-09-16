<?php

namespace App\Application\Trip\DTOs;

use Illuminate\Http\Request;

class CreateTripData
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

    public static function rules(): array
    {
        return [
            // Regras de validação
            // 'name' => 'required|string|max:255',
            // 'email' => 'required|email|unique:users,email',
        ];
    }

    public static function messages(): array
    {
        return [
            // Mensagens em caso de erro
            // 'file.required' => 'O nome é obrigatório',
            // 'email.unique' => 'Esse email já esta cadastrado',
        ];
    }
}