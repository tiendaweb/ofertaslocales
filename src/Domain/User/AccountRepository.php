<?php

declare(strict_types=1);

namespace App\Domain\User;

interface AccountRepository
{
    public function findBusinessAccounts(): array;

    public function findAllPaginated(int $page = 1, int $perPage = 10): array;

    public function countByRole(string $role): int;

    public function findByEmail(string $email): ?array;

    public function findById(int $id): ?array;

    public function create(array $data): array;

    public function update(int $id, array $data): ?array;

    public function updatePassword(int $id, string $passwordHash): bool;

    public function suspend(int $id, int $suspendedBy, ?string $reason = null): bool;

    public function unsuspend(int $id): bool;

    public function createBusinessAccount(array $data): array;
}
