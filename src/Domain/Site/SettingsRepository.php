<?php

declare(strict_types=1);

namespace App\Domain\Site;

interface SettingsRepository
{
    public function findAll(): array;

    public function findByKeys(array $keys): array;

    public function updateMany(array $settings, array $auditContext = []): void;

    public function findAuditLogs(array $keys, int $limit = 50): array;
}
