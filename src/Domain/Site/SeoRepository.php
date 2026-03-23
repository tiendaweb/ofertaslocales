<?php

declare(strict_types=1);

namespace App\Domain\Site;

interface SeoRepository
{
    public function findAll(): array;

    public function findByPage(string $pageName): ?array;

    public function updatePage(string $pageName, array $data): void;
}
