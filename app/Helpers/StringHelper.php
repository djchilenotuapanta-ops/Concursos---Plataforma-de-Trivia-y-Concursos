<?php

namespace App\Helpers;

class StringHelper
{
    public static function normalizeString(?string $value): string
    {
        return strtolower(trim((string) $value));
    }

    public static function removeSpaces(?string $value): string
    {
        return preg_replace('/\s+/', '', static::normalizeString($value));
    }

    public static function cleanNumeric(?string $value): string
    {
        return preg_replace('/\D+/', '', (string) $value);
    }

    public static function toLike(string $value): string
    {
        return '%' . $value . '%';
    }
}
