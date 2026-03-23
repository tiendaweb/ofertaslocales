<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Business;

use App\Domain\Business\BusinessDirectoryRepository;
use PDO;

class SqliteBusinessDirectoryRepository implements BusinessDirectoryRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findBusinessesWithActiveOffers(): array
    {
        $statement = $this->pdo->query(
            "SELECT users.id, users.email, users.role, users.business_name, users.whatsapp, users.created_at,
                    COUNT(offers.id) AS active_offers,
                    MIN(offers.expires_at) AS next_expiration
             FROM users
             INNER JOIN offers ON offers.user_id = users.id
             WHERE users.role = 'business'
               AND offers.status = 'active'
               AND datetime(offers.expires_at) > datetime('now')
             GROUP BY users.id, users.email, users.role, users.business_name, users.whatsapp, users.created_at
             ORDER BY active_offers DESC, users.business_name ASC"
        );

        return $statement->fetchAll();
    }

    public function countBusinessesWithActiveOffers(): int
    {
        $statement = $this->pdo->query(
            "SELECT COUNT(*)
             FROM (
                 SELECT users.id
                 FROM users
                 INNER JOIN offers ON offers.user_id = users.id
                 WHERE users.role = 'business'
                   AND offers.status = 'active'
                   AND datetime(offers.expires_at) > datetime('now')
                 GROUP BY users.id
             ) active_businesses"
        );

        return (int) $statement->fetchColumn();
    }
}
