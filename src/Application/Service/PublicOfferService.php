<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Offer\PublicOfferRepository;

class PublicOfferService
{
    public function __construct(private readonly PublicOfferRepository $publicOfferRepository)
    {
    }

    public function getActiveOffers(): array
    {
        return $this->publicOfferRepository->findActiveOffers();
    }

    public function getActiveMapOffers(): array
    {
        return $this->publicOfferRepository->findActiveOffersForMap();
    }

    public function getActiveCategories(): array
    {
        $categories = array_map(
            static fn (array $offer): string => (string) $offer['category'],
            $this->getActiveOffers()
        );

        $categories = array_values(array_unique($categories));
        sort($categories);

        return $categories;
    }
}
