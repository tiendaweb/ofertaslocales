<?php

declare(strict_types=1);

namespace App\Domain\Category;

interface CategoryRepository
{
    public function findApprovedNames(): array;

    public function findAll(): array;

    public function findPending(): array;

    public function createApproved(string $name, int $adminUserId): bool;

    public function requestCategory(string $name, int $requestedByUserId): bool;

    public function updateStatus(int $categoryId, string $status, int $adminUserId): bool;

    public function isApproved(string $name): bool;
}
