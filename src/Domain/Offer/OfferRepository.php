<?php

declare(strict_types=1);

namespace App\Domain\Offer;

interface OfferRepository
{
    public function countPendingOffers(): int;
}
