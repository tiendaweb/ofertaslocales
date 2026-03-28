<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Category\CategoryRepository;
use App\Domain\Offer\PublicOfferRepository;
use DateTimeImmutable;

class PublicCatalogService
{
    public function __construct(
        private readonly PublicOfferRepository $publicOfferRepository,
        private readonly CategoryRepository $categoryRepository
    ) {
    }

    public function buildCatalog(?int $selectedBusinessId = null): array
    {
        $rawOffers = $this->publicOfferRepository->findActiveOffers();
        $offers = array_map([$this, 'normalizeOffer'], $rawOffers);
        $categories = $this->extractCategories($offers);
        $businesses = $this->buildBusinesses($offers);
        $mapOffers = $this->buildMapOffers($offers);

        $visibleOffers = $selectedBusinessId === null
            ? $offers
            : array_values(array_filter(
                $offers,
                static fn (array $offer): bool => $offer['user_id'] === $selectedBusinessId
            ));

        $selectedBusiness = $selectedBusinessId === null
            ? null
            : $this->findBusinessById($businesses, $selectedBusinessId);

        return [
            'offers' => $visibleOffers,
            'allOffers' => $offers,
            'categories' => $categories,
            'businesses' => $businesses,
            'mapOffers' => $mapOffers,
            'selectedBusiness' => $selectedBusiness,
            'selectedBusinessId' => $selectedBusinessId,
            'defaultCenter' => $this->resolveDefaultCenter($mapOffers),
            'metrics' => [
                'activeOffers' => count($offers),
                'estimatedContacts' => max(count($offers) * 4, 12),
                'activeBusinesses' => count($businesses),
            ],
        ];
    }

    private function normalizeOffer(array $offer): array
    {
        $expiresAt = new DateTimeImmutable((string) $offer['expires_at']);

        return [
            'id' => (int) $offer['id'],
            'user_id' => (int) $offer['user_id'],
            'business_name' => (string) $offer['business_name'],
            'category' => (string) $offer['category'],
            'title' => (string) $offer['title'],
            'description' => (string) $offer['description'],
            'image_url' => (string) ($offer['image_url'] ?: 'https://placehold.co/900x600/f3f4f6/1f2937?text=Oferta'),
            'whatsapp' => (string) $offer['whatsapp'],
            'location' => (string) $offer['location'],
            'lat' => $offer['lat'] !== null ? (float) $offer['lat'] : null,
            'lon' => $offer['lon'] !== null ? (float) $offer['lon'] : null,
            'expires_at' => $expiresAt->format(DATE_ATOM),
            'expires_label' => 'Vence ' . $expiresAt->format('d/m H:i'),
            'badge' => $this->resolveBadge((string) $offer['category'], $expiresAt),
            'business_bio' => $offer['bio'] !== null ? (string) $offer['bio'] : null,
            'business_instagram_url' => $offer['instagram_url'] !== null ? (string) $offer['instagram_url'] : null,
            'business_facebook_url' => $offer['facebook_url'] !== null ? (string) $offer['facebook_url'] : null,
            'business_tiktok_url' => $offer['tiktok_url'] !== null ? (string) $offer['tiktok_url'] : null,
            'business_website_url' => $offer['website_url'] !== null ? (string) $offer['website_url'] : null,
            'business_logo_url' => $offer['logo_url'] !== null ? (string) $offer['logo_url'] : null,
            'business_cover_url' => $offer['cover_url'] !== null ? (string) $offer['cover_url'] : null,
        ];
    }

    private function extractCategories(array $offers): array
    {
        $categories = array_values(array_unique(array_map(
            static fn (array $offer): string => $offer['category'],
            $offers
        )));
        sort($categories);

        return $categories;
    }

    private function buildBusinesses(array $offers): array
    {
        $businesses = [];

        foreach ($offers as $offer) {
            $businessId = $offer['user_id'];

            if (!isset($businesses[$businessId])) {
                $businesses[$businessId] = [
                    'id' => $businessId,
                    'business_name' => $offer['business_name'],
                    'whatsapp' => $offer['whatsapp'],
                    'location' => $offer['location'],
                    'category' => $offer['category'],
                    'active_offers' => 0,
                    'next_expiration' => $offer['expires_at'],
                    'next_expiration_label' => $offer['expires_label'],
                    'bio' => $offer['business_bio'],
                    'instagram_url' => $offer['business_instagram_url'],
                    'facebook_url' => $offer['business_facebook_url'],
                    'tiktok_url' => $offer['business_tiktok_url'],
                    'website_url' => $offer['business_website_url'],
                    'logo_url' => $offer['business_logo_url'],
                    'cover_url' => $offer['business_cover_url'],
                    'cover_image_url' => $offer['business_cover_url'] ?: $offer['image_url'],
                    'active_publications' => [],
                ];
            }

            $businesses[$businessId]['active_offers']++;

            if ($offer['expires_at'] < $businesses[$businessId]['next_expiration']) {
                $businesses[$businessId]['next_expiration'] = $offer['expires_at'];
                $businesses[$businessId]['next_expiration_label'] = $offer['expires_label'];
                $businesses[$businessId]['location'] = $offer['location'];
                $businesses[$businessId]['category'] = $offer['category'];
            }

            $businesses[$businessId]['active_publications'][] = [
                'id' => $offer['id'],
                'title' => $offer['title'],
                'category' => $offer['category'],
                'location' => $offer['location'],
                'expires_at' => $offer['expires_at'],
                'expires_label' => $offer['expires_label'],
                'image_url' => $offer['image_url'],
            ];
        }

        $businesses = array_values($businesses);

        usort($businesses, static function (array $left, array $right): int {
            $countComparison = $right['active_offers'] <=> $left['active_offers'];

            if ($countComparison !== 0) {
                return $countComparison;
            }

            return strcasecmp($left['business_name'], $right['business_name']);
        });

        return $businesses;
    }

    private function buildMapOffers(array $offers): array
    {
        return array_values(array_filter(
            array_map(function (array $offer): ?array {
                if ($offer['lat'] === null || $offer['lon'] === null) {
                    return null;
                }

                return [
                    'id' => $offer['id'],
                    'user_id' => $offer['user_id'],
                    'business_name' => $offer['business_name'],
                    'category' => $offer['category'],
                    'title' => $offer['title'],
                    'description' => $offer['description'],
                    'image_url' => $offer['image_url'],
                    'whatsapp' => $offer['whatsapp'],
                    'location' => $offer['location'],
                    'lat' => $offer['lat'],
                    'lon' => $offer['lon'],
                    'expires_at' => $offer['expires_at'],
                    'expires_label' => $offer['expires_label'],
                ];
            }, $offers)
        ));
    }

    private function findBusinessById(array $businesses, int $selectedBusinessId): ?array
    {
        foreach ($businesses as $business) {
            if ($business['id'] === $selectedBusinessId) {
                return $business;
            }
        }

        return null;
    }

    private function resolveDefaultCenter(array $mapOffers): array
    {
        if ($mapOffers === []) {
            return [-34.636, -58.536];
        }

        return [(float) $mapOffers[0]['lat'], (float) $mapOffers[0]['lon']];
    }

    private function resolveBadge(string $category, DateTimeImmutable $expiresAt): string
    {
        $remainingSeconds = $expiresAt->getTimestamp() - time();

        if ($remainingSeconds <= 14_400) {
            return '⏳ ÚLTIMAS HORAS';
        }

        return match ($category) {
            'Gastronomía' => '🍕 IDEAL CENA',
            'Ferretería' => '🔥 MÁS VENDIDO',
            'Estética' => '✂️ TENDENCIA',
            'Deportes' => '🏃 MODO ACTIVO',
            'Panadería' => '🥐 RECIÉN HECHO',
            default => '✨ RECOMENDADA',
        };
    }
}
