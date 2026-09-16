<?php

namespace App\Domain\Trip\Enums;

enum TripType: string
{
    case GOING = 'going';
    case RETURNING = 'returning';

    public function label(): string
    {
        return match($this) {
            self::GOING => 'Ida',
            self::RETURNING => 'Volta',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function fromString(string $type): self
    {
        return match($type) {
            'going' => self::GOING,
            'returning' => self::RETURNING,
            default => throw new \InvalidArgumentException("Invalid type: {$type}"),
        };
    }
}