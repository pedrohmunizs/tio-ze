<?php

namespace App\Application\Child\DTOs;

use Illuminate\Http\Request;

class CreateChildData
{
    public function __construct(
        public readonly string $name,
        public readonly string $grade,
        public readonly string $zip_code,
        public readonly string $street,
        public readonly string $number,
        public readonly string $neighborhood,
        public readonly string $city,
        public readonly string $state,
        public readonly string $fk_school,
        public readonly ?string $phone = null,
        public readonly ?int $fk_parent = null,
        public readonly ?string $complement = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $request->validate(self::rules(), self::messages());

        return new self(
            name: $request->input('name'),
            grade: $request->input('grade'),
            phone: $request->input('phone'),
            zip_code: $request->input('zip_code'),
            street: $request->input('street'),
            number: $request->input('number'),
            complement: $request->input('complement'),
            neighborhood: $request->input('neighborhood'),
            city: $request->input('city'),
            state: $request->input('state'),
            fk_parent: $request->input('fk_parent'),
            fk_school: $request->input('fk_school'),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'grade' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|size:2',
            'fk_parent' => 'nullable|numeric',
            'fk_school' => 'required|numeric',
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'O nome da rota é obrigatório',
            'phone.required' => 'O telefone é obrigatório',
            'phone.max' => 'Número detelefone muito grande',
            'grade.required' => 'A turma/série do aluno é obrigatório',
            'fk_school.required' => 'A escola é obrigatória',
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