<?php

namespace App\Application\TransportRequest\DTOs;

use Illuminate\Http\Request;

class CreateTransportRequestData
{
    public function __construct(
        public readonly int $fk_route,
        public readonly int $fk_student,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            fk_route: $request->input('fk_route'),
            fk_student: $request->input('fk_student'),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function rules(): array
    {
        return [
            'fk_route' => 'required|numeric',
            'fk_student' => 'required|numeric',
        ];
    }

    public static function messages(): array
    {
        return [
            'fk_route.required' => 'A rota é obrigatório',
            'fk_student.required' => 'O estudante é obrigatório',
        ];
    }
}