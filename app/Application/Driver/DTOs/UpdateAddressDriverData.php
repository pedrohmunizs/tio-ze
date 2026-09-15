<?php

namespace App\Application\Driver\DTOs;

use Illuminate\Http\Request;

class UpdateAddressDriverData
{
    public function __construct(
        public readonly string $zip_code,
        public readonly string $street,
        public readonly string $number,
        public readonly string $neighborhood,
        public readonly string $city,
        public readonly string $state,
        public readonly ?string $complement = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $request->validate(self::rules(), self::messages());

        return new self(
            zip_code: $request->input('zip_code'),
            street: $request->input('street'),
            number: $request->input('number'),
            neighborhood: $request->input('neighborhood'),
            city: $request->input('city'),
            state: $request->input('state'),
            complement: $request->input('complement'),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function rules(): array
    {
        return [
            'zip_code' => 'required|string|max:10',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|size:2',
        ];
    }

    public static function messages(): array
    {
        return [
            'zip_code.required' => 'o CEP é obrigatório',
            'street.required' => 'A rua é obrigatória',
            'number.required' => 'O número é obrigatório',
            'neighborhood.required' => 'O bairro é obrigatório',
            'city.required' => 'A cidade é obrigatória',
            'state.required' => 'O estado é obrigatório',
            'state.max' => 'Utilize somente duas letras',
        ];
    }
}