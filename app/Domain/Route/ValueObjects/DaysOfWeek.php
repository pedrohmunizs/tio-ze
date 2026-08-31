<?php
// app/Domain/Route/ValueObjects/DaysOfWeek.php

namespace App\Domain\Route\ValueObjects;

class DaysOfWeek
{
    private array $days;

    private const VALID_DAYS = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'];

    public function __construct(array|string $days)
    {
        if (is_string($days)) {
            $days = explode(',', $days);
        }

        $this->validate($days);
        $this->days = $days;
    }

    private function validate(array $days): void
    {
        if (empty($days)) {
            throw new \InvalidArgumentException('At least one day must be selected');
        }

        foreach ($days as $day) {
            $day = trim($day);
            if (!in_array($day, self::VALID_DAYS)) {
                throw new \InvalidArgumentException("Invalid day: {$day}");
            }
        }
    }

    public function toArray(): array
    {
        return $this->days;
    }

    public function toString(): string
    {
        return implode(',', $this->days);
    }

    public function contains(string $day): bool
    {
        return in_array($day, $this->days);
    }

    public function equals(DaysOfWeek $other): bool
    {
        return $this->days === $other->toArray();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}