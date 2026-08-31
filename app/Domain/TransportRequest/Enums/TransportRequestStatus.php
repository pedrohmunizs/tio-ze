<?php

namespace App\Domain\TransportRequest\Enums;

enum TransportRequestStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendente',
            self::ACCEPTED => 'Aceito',
            self::REJECTED => 'Rejeitado',
            self::CANCELLED => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::ACCEPTED => 'green',
            self::REJECTED => 'red',
            self::CANCELLED => 'red',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}