<?php

declare(strict_types=1);

namespace App\Application\Support;

final class Whatsapp
{
    public function normalize(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }

    public function isValid(string $normalizedWhatsapp): bool
    {
        $length = strlen($normalizedWhatsapp);

        return $length >= 10 && $length <= 15;
    }

    public function buildUrl(string $normalizedWhatsapp): string
    {
        return sprintf('https://wa.me/%s', $normalizedWhatsapp);
    }
}
