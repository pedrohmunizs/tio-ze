<?php

namespace App\Domain\Vehicle\Enums;

enum VehicleStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case MAINTENANCE = 'maintenance';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Ativo',
            self::INACTIVE => 'Inativo',
            self::MAINTENANCE => 'Manutenção',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}