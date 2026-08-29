<?php

namespace App\Support;

class RegistrationNumber
{
    public static function normalize(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return strtoupper(trim($value));
    }
}
