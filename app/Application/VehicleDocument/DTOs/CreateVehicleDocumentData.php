<?php

namespace App\Application\VehicleDocument\DTOs;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class CreateVehicleDocumentData
{
    public function __construct(
        public readonly int $fk_vehicle,
        public readonly string $type,
        public readonly UploadedFile $file,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            fk_vehicle: $request->input('fk_vehicle'),
            type: $request->input('type'),
            file: $request->file('file'),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function rules(): array
    {
        return [
            'fk_vehicle' => 'required|numeric',
            'type' => 'required|in:registration, insurance, inspection, other',
            'file' => [
                'required',
                'file',
                'max:10240',
                'mimes:pdf,jpg,jpeg,png',
            ],
        ];
    }

    public static function messages(): array
    {
        return [
            'file.required' => 'A imagem do documento é obrigatória',
            'file.max' => 'A imagem não pode ter mais que 10MB',
            'file.mimes' => 'A imagem deve ser JPEG, PNG, JPG ou GIF',
            'type.required' => 'Indique qual é o documento',
            'fk_vehicle.required' => 'Indique qual é o veículo',
        ];
    }
}