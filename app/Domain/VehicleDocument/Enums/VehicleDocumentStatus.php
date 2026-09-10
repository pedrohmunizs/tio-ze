<?php

namespace App\Domain\VehicleDocument\Enums;

enum VehicleDocumentStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendente',
            self::APPROVED => 'Aprovado',
            self::REJECTED => 'Rejeitado',
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
            'approved' => self::APPROVED,
            'rejected' => self::REJECTED,
            default => throw new \InvalidArgumentException("Invalid status: {$status}"),
        };
    }
}