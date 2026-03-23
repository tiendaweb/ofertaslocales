<?php

declare(strict_types=1);

namespace App\Domain\Site;

interface SettingsRepository
{
    public function findAll(): array;

    public function findByKeys(array $keys): array;
}
