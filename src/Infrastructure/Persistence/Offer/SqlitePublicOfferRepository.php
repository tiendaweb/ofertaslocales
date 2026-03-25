<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Offer;

use App\Domain\Offer\PublicOfferRepository;
use PDO;

class SqlitePublicOfferRepository implements PublicOfferRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findActiveOffers(): array
    {
        $statement = $this->pdo->query(
            "SELECT offers.id, offers.user_id, offers.category, offers.title, offers.description, offers.image_url,
                    offers.whatsapp, offers.location, offers.lat, offers.lon, offers.status, offers.created_at,
                    offers.expires_at, users.business_name, users.bio, users.instagram_url, users.facebook_url,
                    users.tiktok_url, users.website_url, users.logo_url, users.cover_url
             FROM offers
             INNER JOIN users ON users.id = offers.user_id
             WHERE offers.status = 'active'
               AND datetime(offers.expires_at) > datetime('now')
             ORDER BY offers.expires_at ASC, offers.created_at DESC"
        );

        return $statement->fetchAll();
    }

    public function findActiveOffersForMap(): array
    {
        $statement = $this->pdo->query(
            "SELECT offers.id, offers.user_id, offers.category, offers.title, offers.description, offers.image_url,
                    offers.whatsapp, offers.location, offers.lat, offers.lon, offers.created_at, offers.expires_at,
                    users.business_name, users.bio, users.instagram_url, users.facebook_url, users.tiktok_url,
                    users.website_url, users.logo_url, users.cover_url
             FROM offers
             INNER JOIN users ON users.id = offers.user_id
             WHERE offers.status = 'active'
               AND datetime(offers.expires_at) > datetime('now')
               AND offers.lat IS NOT NULL
               AND offers.lon IS NOT NULL
             ORDER BY offers.expires_at ASC, offers.created_at DESC"
        );

        return $statement->fetchAll();
    }

    public function countActiveOffers(): int
    {
        $statement = $this->pdo->query(
            "SELECT COUNT(*)
             FROM offers
             WHERE status = 'active'
               AND datetime(expires_at) > datetime('now')"
        );

        return (int) $statement->fetchColumn();
    }
}
