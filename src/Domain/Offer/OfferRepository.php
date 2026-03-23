<?php

declare(strict_types=1);

namespace App\Domain\Offer;

interface OfferRepository
{
    public function findFeaturedOffers(): array;

    public function findActiveOffers(): array;

    public function findBusinessesWithOffers(): array;

    public function findMapOffers(): array;

    public function countPendingOffers(): int;
}
