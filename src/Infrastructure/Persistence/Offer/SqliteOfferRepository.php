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

    public function countPendingOffers(): int
    {
        $statement = $this->pdo->query(
            "SELECT COUNT(*)
             FROM offers
             WHERE status = 'pending'
               AND datetime(expires_at) > datetime('now')"
        );

        return (int) $statement->fetchColumn();
    }
}
