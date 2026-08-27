<?php

namespace App\Domain\Child\Enums;

enum ChildStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Ativo',
            self::INACTIVE => 'Inativo',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'green',
            self::INACTIVE => 'gray',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}