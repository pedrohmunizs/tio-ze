<?php

namespace App\Domain\Stop\Enums;

enum StopType: string
{
    case GOING = 'going';
    case RETURNING = 'returning';

    public function label(): string
    {
        return match($this) {
            self::GOING => 'Indo',
            self::RETURNING => 'voltando',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}