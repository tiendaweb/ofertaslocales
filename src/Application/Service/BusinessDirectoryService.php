<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Business\BusinessDirectoryRepository;

class BusinessDirectoryService
{
    public function __construct(private readonly BusinessDirectoryRepository $businessDirectoryRepository)
    {
    }

    public function getBusinessesWithActiveOffers(): array
    {
        return $this->businessDirectoryRepository->findBusinessesWithActiveOffers();
    }

    public function getSummary(): array
    {
        $businesses = $this->getBusinessesWithActiveOffers();

        return [
            'totalBusinesses' => count($businesses),
            'activeOffers' => array_sum(array_map(
                static fn (array $business): int => (int) $business['active_offers'],
                $businesses
            )),
        ];
    }
}
