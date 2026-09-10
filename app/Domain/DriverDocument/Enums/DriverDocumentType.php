<?php

namespace App\Domain\DriverDocument\Enums;

enum DriverDocumentType: string
{
    case LICENSE = 'license';
    case CERTIFICATE = 'certificate';
    case MEDICAL = 'medical';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::LICENSE => 'Registro',
            self::CERTIFICATE => 'Seguro',
            self::MEDICAL => 'Inspeção',
            self::OTHER => 'Outro',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}