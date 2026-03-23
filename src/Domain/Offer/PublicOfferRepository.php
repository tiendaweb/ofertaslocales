<?php

declare(strict_types=1);

namespace App\Domain\Offer;

interface PublicOfferRepository
{
    public function findActiveOffers(): array;

    public function findActiveOffersForMap(): array;

    public function countActiveOffers(): int;
}
