<?php

namespace App\Services;

class ValidationService
{
    public static function validateEmail(?string $email): bool
    {
        if (!$email) {
            return false;
        }

        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validateCedula(?string $cedula): bool
    {
        if (!$cedula) {
            return false;
        }

        $cleanCedula = preg_replace('/\D+/', '', $cedula);

        if (strlen($cleanCedula) !== 10 && strlen($cleanCedula) !== 13) {
            return false;
        }

        return true;
    }

    public static function validateRuc(?string $ruc): bool
    {
        if (!$ruc) {
            return false;
        }

        $cleanRuc = preg_replace('/\D+/', '', $ruc);

        return strlen($cleanRuc) >= 10 && strlen($cleanRuc) <= 13;
    }

    public static function validatePhone(?string $phone): bool
    {
        if (!$phone) {
            return false;
        }

        $cleanPhone = preg_replace('/\D+/', '', $phone);

        return strlen($cleanPhone) >= 7 && strlen($cleanPhone) <= 15;
    }

    public static function sanitizeString(?string $value): string
    {
        if (!$value) {
            return '';
        }

        return trim(strip_tags($value));
    }

    public static function sanitizeHtml(?string $value): string
    {
        if (!$value) {
            return '';
        }

        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
