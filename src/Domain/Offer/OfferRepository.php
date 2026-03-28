<?php

declare(strict_types=1);

namespace App\Domain\Offer;

interface OfferRepository
{
    public function countPendingOffers(): int;

    public function findByUserId(int $userId): array;

    public function createForUser(int $userId, array $data): int;

    public function findForModeration(): array;

    public function updateStatus(int $offerId, string $status): void;

    public function updateByAdmin(int $offerId, array $data): bool;

    public function updateForUser(int $offerId, int $userId, array $data): bool;

    public function updateStatusForUser(int $offerId, int $userId, string $status): bool;

    public function duplicateForUser(int $offerId, int $userId): ?int;

    public function softDeleteForUser(int $offerId, int $userId): bool;

    public function expireOffers(): int;
}
