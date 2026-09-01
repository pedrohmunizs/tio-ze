<?php

namespace App\Domain\Contract\Enums;

enum ContractStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending';
    case BLOCKED = 'blocked';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Ativo',
            self::INACTIVE => 'Inativo',
            self::PENDING => 'Pendente',
            self::BLOCKED => 'Bloqueado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'green',
            self::INACTIVE => 'gray',
            self::PENDING => 'yellow',
            self::BLOCKED => 'red',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}