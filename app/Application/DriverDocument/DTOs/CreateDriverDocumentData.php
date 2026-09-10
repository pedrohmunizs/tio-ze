<?php

namespace App\Application\DriverDocument\DTOs;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class CreateDriverDocumentData
{
    public function __construct(
        public readonly ?int $fk_driver = null,
        public readonly string $type,
        public readonly Carbon $valid_until,
        public readonly UploadedFile $file,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $request->validate(
            self::rules(),
            self::messages()
        );

        return new self(
            fk_driver: $request->input('fk_driver') ? (int) $request->input('fk_driver') : null,
            type: $request->input('type'),
            valid_until: Carbon::createFromFormat('d/m/Y', $request->input('valid_until')),
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
            'fk_driver' => 'nullable|numeric',
            'type' => 'required|in:license,certificate,medical,other',
            'valid_until' => 'required|date_format:d/m/Y',
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
            'type.in' => 'Indique um documento válido',
            'valid_until.required' => 'Indique qual até qual data o documento é valido',
        ];
    }
}