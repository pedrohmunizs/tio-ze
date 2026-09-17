<?php

namespace App\Domain\TripStudent\Enums;

enum TripStudentStatus: string
{
    case PENDING = 'pending';
    case PICKEDUP = 'picked_up';
    case DROPPEDOFF = 'dropped_off';
    case ABSENT = 'absent';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendente',
            self::PICKEDUP => 'Pegou',
            self::DROPPEDOFF => 'Deixou',
            self::ABSENT => 'Ausente',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function fromString(string $status): self
    {
        return match($status) {
            'pending' => self::PENDING,
            'picked_up' => self::PICKEDUP,
            'dropped_off' => self::DROPPEDOFF,
            'absent' => self::ABSENT,
            default => throw new \InvalidArgumentException("Invalid status: {$status}"),
        };
    }
}