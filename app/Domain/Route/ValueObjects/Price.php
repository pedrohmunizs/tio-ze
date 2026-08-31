<?php

namespace App\Domain\Route\ValueObjects;

class Price
{
    private float $value;

    public function __construct(float $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Price cannot be negative');
        }
        $this->value = $value;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getFormatted(): string
    {
        return 'R$ ' . number_format($this->value, 2, ',', '.');
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}