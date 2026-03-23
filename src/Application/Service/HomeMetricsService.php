<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Business\BusinessDirectoryRepository;
use App\Domain\Offer\PublicOfferRepository;

class HomeMetricsService
{
    public function __construct(
        private readonly PublicOfferRepository $publicOfferRepository,
        private readonly BusinessDirectoryRepository $businessDirectoryRepository
    ) {
    }

    public function getMetrics(): array
    {
        $activeOffers = $this->publicOfferRepository->countActiveOffers();
        $activeBusinesses = $this->businessDirectoryRepository->countBusinessesWithActiveOffers();

        return [
            'activeOffers' => $activeOffers,
            'estimatedContacts' => max($activeOffers * 4, 12),
            'activeBusinesses' => $activeBusinesses,
        ];
    }
}
