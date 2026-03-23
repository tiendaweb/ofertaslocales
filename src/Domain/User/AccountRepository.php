<?php

declare(strict_types=1);

namespace App\Domain\User;

interface AccountRepository
{
    public function findBusinessAccounts(): array;

    public function countByRole(string $role): int;

    public function findByEmail(string $email): ?array;

    public function findById(int $id): ?array;

    public function createBusinessAccount(array $data): array;
}
