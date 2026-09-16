<?php

namespace App\Domain\Trip\Enums;

enum TripStatus: string
{
    case SCHEDULED = 'scheduled';
    case STARTED = 'started';
    case PAUSED = 'paused';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::SCHEDULED => 'Agendado',
            self::STARTED => 'Iniciado',
            self::PAUSED => 'pausado',
            self::COMPLETED => 'Completo',
            self::CANCELLED => 'Cancelado',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function fromString(string $status): self
    {
        return match($status) {
            'scheduled' => self::SCHEDULED,
            'started' => self::STARTED,
            'paused' => self::PAUSED,
            'completed' => self::COMPLETED,
            'cancelled' => self::CANCELLED,
            default => throw new \InvalidArgumentException("Invalid status: {$status}"),
        };
    }
}