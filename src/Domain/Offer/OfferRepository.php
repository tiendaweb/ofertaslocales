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

    public function expireOffers(): int;
}
