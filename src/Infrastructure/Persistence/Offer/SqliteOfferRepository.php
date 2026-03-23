<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Offer;

use App\Domain\Offer\OfferRepository;
use PDO;

class SqliteOfferRepository implements OfferRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findFeaturedOffers(): array
    {
        $statement = $this->pdo->query(
            "SELECT offers.id, offers.category, offers.title, offers.description, offers.image_url, offers.whatsapp,
                    offers.location, offers.lat, offers.lon, offers.status, offers.expires_at,
                    users.business_name
             FROM offers
             INNER JOIN users ON users.id = offers.user_id
             WHERE offers.status = 'active'
             ORDER BY offers.expires_at ASC
             LIMIT 3"
        );

        return $statement->fetchAll();
    }

    public function findActiveOffers(): array
    {
        $statement = $this->pdo->query(
            "SELECT offers.id, offers.category, offers.title, offers.description, offers.image_url, offers.whatsapp,
                    offers.location, offers.lat, offers.lon, offers.status, offers.expires_at,
                    users.business_name
             FROM offers
             INNER JOIN users ON users.id = offers.user_id
             WHERE offers.status = 'active'
             ORDER BY offers.category ASC, offers.expires_at ASC"
        );

        return $statement->fetchAll();
    }

    public function findBusinessesWithOffers(): array
    {
        $statement = $this->pdo->query(
            "SELECT users.id, users.business_name, users.email, users.role,
                    COUNT(offers.id) AS active_offers,
                    MIN(offers.expires_at) AS next_expiration
             FROM users
             LEFT JOIN offers ON offers.user_id = users.id AND offers.status = 'active'
             WHERE users.role IN ('business', 'admin')
             GROUP BY users.id, users.business_name, users.email, users.role
             ORDER BY active_offers DESC, users.business_name ASC"
        );

        return $statement->fetchAll();
    }

    public function findMapOffers(): array
    {
        $statement = $this->pdo->query(
            "SELECT offers.id, offers.category, offers.title, offers.description, offers.image_url, offers.whatsapp,
                    offers.location, offers.lat, offers.lon, offers.expires_at, users.business_name
             FROM offers
             INNER JOIN users ON users.id = offers.user_id
             WHERE offers.status = 'active'
               AND offers.lat IS NOT NULL
               AND offers.lon IS NOT NULL
             ORDER BY offers.expires_at ASC"
        );

        return $statement->fetchAll();
    }

    public function countPendingOffers(): int
    {
        $statement = $this->pdo->query("SELECT COUNT(*) FROM offers WHERE status = 'pending'");

        return (int) $statement->fetchColumn();
    }
}
