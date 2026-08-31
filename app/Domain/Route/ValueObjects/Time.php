<?php

namespace App\Domain\Route\ValueObjects;

class Time
{
    private string $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        if (!preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/', $value)) {
            throw new \InvalidArgumentException("Invalid time format: {$value}. Use HH:MM");
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isBefore(Time $other): bool
    {
        return $this->value < $other->getValue();
    }

    public function isAfter(Time $other): bool
    {
        return $this->value > $other->getValue();
    }

    public function equals(Time $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}