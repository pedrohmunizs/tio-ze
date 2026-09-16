<?php

namespace App\Application\Trip\DTOs;

use Illuminate\Http\Request;

class GenerateWeeklyTripData
{
    public function __construct(
        public readonly int $id,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $request->validate(self::rules(), self::messages());

        return new self(
            id: (int) $request->input('id'),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function rules(): array
    {
        return [
        ];
    }

    public static function messages(): array
    {
        return [
        ];
    }
}