<?php

namespace App\Application\User\DTOs;

use Illuminate\Http\Request;

class UserData
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $cpf = null,
        public readonly ?string $phone = null,
        public readonly ?string $password = null,
        public readonly ?int $addressId = null,
        public readonly ?string $status = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            id: $request->input('id'),
            name: $request->input('name'),
            email: $request->input('email'),
            cpf: $request->input('cpf'),
            phone: $request->input('phone'),
            password: $request->input('password'),
            addressId: $request->input('address_id'),
            status: $request->input('status'),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            cpf: $data['cpf'] ?? null,
            phone: $data['phone'] ?? null,
            password: $data['password'] ?? null,
            addressId: $data['address_id'] ?? null,
            status: $data['status'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn($value) => $value !== null);
    }

    public function validate(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|string|size:11|unique:users,cpf',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ];
    }
}