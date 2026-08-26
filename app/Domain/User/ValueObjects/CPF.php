<?php

namespace App\Domain\User\ValueObjects;

class CPF
{
    private string $value;

    public function __construct(string $value)
    {
        $clean = preg_replace('/[^0-9]/', '', $value);
        
        if (strlen($clean) !== 11) {
            throw new \InvalidArgumentException('CPF must have 11 digits');
        }
        
        if (!$this->validateCpf($clean)) {
            throw new \InvalidArgumentException('Invalid CPF');
        }
        
        $this->value = $clean;
    }

    private function validateCpf(string $cpf): bool
    {
        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Calcula o primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $cpf[$i] * (10 - $i);
        }
        $remainder = $sum % 11;
        $digit1 = $remainder < 2 ? 0 : 11 - $remainder;

        // Calcula o segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $cpf[$i] * (11 - $i);
        }
        $remainder = $sum % 11;
        $digit2 = $remainder < 2 ? 0 : 11 - $remainder;

        return $cpf[9] == $digit1 && $cpf[10] == $digit2;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getFormatted(): string
    {
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}