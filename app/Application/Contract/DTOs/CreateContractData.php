<?php

namespace App\Application\Contract\DTOs;

use Illuminate\Http\Request;

class CreateContractData
{
    public function __construct(
        public readonly int $fk_student,
        public readonly int $fk_route,
        public readonly int $fk_provider,
        public readonly int $fk_transport_request,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            fk_student: $request->input('fk_student'),
            fk_route: $request->input('fk_route'),
            fk_provider: $request->input('fk_provider'),
            fk_transport_request: $request->input('fk_transport_request'),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            fk_student: (int) ($data['fk_student']),
            fk_route: (int) ($data['fk_route']),
            fk_provider: (int) ($data['fk_provider']),
            fk_transport_request: (int) ($data['fk_transport_request'])
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function validate(): array
    {
        return [
            'fk_student' => 'required|numeric',
            'fk_route' => 'required|numeric',
            'fk_provider' => 'required|numeric',
            'fk_transport_request' => 'required|numeric',
        ];
    }
}