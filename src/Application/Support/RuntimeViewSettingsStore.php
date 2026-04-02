<?php

declare(strict_types=1);

namespace App\Application\Support;

final class RuntimeViewSettingsStore
{
    /**
     * @var array<string, string>
     */
    private static array $settings = [];

    /**
     * @param array<string, string> $settings
     */
    public static function set(array $settings): void
    {
        self::$settings = $settings;
    }

    /**
     * @return array<string, string>
     */
    public static function all(): array
    {
        return self::$settings;
    }
}
