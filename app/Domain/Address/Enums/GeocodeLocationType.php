<?php

namespace App\Domain\Address\Enums;

enum GeocodeLocationType: string
{
    case ROOFTOP = 'ROOFTOP';
    case RANGE_INTERPOLATED = 'RANGE_INTERPOLATED';
    case GEOMETRIC_CENTER = 'GEOMETRIC_CENTER';
    case APPROXIMATE = 'APPROXIMATE';

    public function label(): string
    {
        return match($this) {
            self::ROOFTOP => 'Preciso (endereço exato)',
            self::RANGE_INTERPOLATED => 'Interpolado (estimativa na rua)',
            self::GEOMETRIC_CENTER => 'Centro geométrico (rua ou região)',
            self::APPROXIMATE => 'Aproximado (região)',
        };
    }

    public static function fromString(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        try {
            return self::from($value);
        } catch (\ValueError $e) {
            return null;
        }
    }

    public function isPrecise(): bool
    {
        return $this === self::ROOFTOP;
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}