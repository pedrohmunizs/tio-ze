<?php

namespace App\Domain\VehicleDocument\Enums;

enum VehicleDocumentType: string
{
    case REGISTRATION = 'registration';
    case INSURANCE = 'insurance';
    case INSPECTION = 'inspection';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::REGISTRATION => 'Registro',
            self::INSURANCE => 'Seguro',
            self::INSPECTION => 'Inspeção',
            self::OTHER => 'Outro',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}