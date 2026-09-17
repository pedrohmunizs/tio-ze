<?php

namespace App\Application\TripStudent\DTOs;

use Illuminate\Http\Request;

class CreateTripStudentData
{
    public function __construct(
        public readonly int $fk_trip,
        public readonly int $fk_student,
    ) {}

    public static function fromRequest(
        int $fk_trip,
        int $fk_student
    ): self
    {
        return new self(
            fk_trip: $fk_trip,
            fk_student: $fk_student
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