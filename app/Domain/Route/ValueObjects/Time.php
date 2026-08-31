<?php

namespace App\Domain\Route\ValueObjects;

use Carbon\Carbon;

class Time
{
    private string $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $this->normalize($value);
    }

    private function validate(string $value): void
    {
        try {
            Carbon::createFromFormat('H:i:s', $value);
        } catch (\Exception $e) {
            try {
                Carbon::createFromFormat('H:i', $value);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException("Invalid time format: {$value}. Use HH:MM or HH:MM:SS");
            }
        }
    }

    private function normalize(string $value): string
    {
        try {
            $carbon = Carbon::createFromFormat('H:i:s', $value);
            return $carbon->format('H:i');
        } catch (\Exception $e) {
            return $value;
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