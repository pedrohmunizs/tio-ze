<?php

namespace App\Domain\Driver\Enums;

enum DriverStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending';
    case SUSPENDED = 'suspended';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Ativo',
            self::INACTIVE => 'Inativo',
            self::PENDING => 'Pendente',
            self::SUSPENDED => 'Bloqueado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'green',
            self::INACTIVE => 'gray',
            self::PENDING => 'yellow',
            self::SUSPENDED => 'red',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function fromString(string $status): self
    {
        return match($status) {
            'active' => self::ACTIVE,
            'inactive' => self::INACTIVE,
            'pending' => self::PENDING,
            'suspended' => self::SUSPENDED,
            default => throw new \InvalidArgumentException("Invalid status: {$status}"),
        };
    }
}