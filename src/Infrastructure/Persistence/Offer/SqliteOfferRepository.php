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

    public function findByUserId(int $userId): array
    {
        $statement = $this->pdo->prepare(
            "SELECT id, category, title, description, image_url,
                    whatsapp, location, lat, lon, status, created_at, expires_at
             FROM offers
             WHERE user_id = :user_id
             ORDER BY datetime(created_at) DESC"
        );
        $statement->execute(['user_id' => $userId]);

        return $statement->fetchAll();
    }

    public function createForUser(int $userId, array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO offers (
                user_id, category, title, description, image_url,
                whatsapp, location, lat, lon, status, created_at, expires_at
            ) VALUES (
                :user_id, :category, :title, :description, :image_url,
                :whatsapp, :location, :lat, :lon, :status, :created_at, :expires_at
            )'
        );

        $statement->execute([
            'user_id' => $userId,
            'category' => trim((string) $data['category']),
            'title' => trim((string) $data['title']),
            'description' => trim((string) $data['description']),
            'image_url' => $data['image_url'],
            'whatsapp' => trim((string) $data['whatsapp']),
            'location' => trim((string) $data['location']),
            'lat' => $data['lat'],
            'lon' => $data['lon'],
            'status' => (string) $data['status'],
            'created_at' => gmdate('Y-m-d H:i:s'),
            'expires_at' => (string) $data['expires_at'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function findForModeration(): array
    {
        $statement = $this->pdo->query(
            "SELECT offers.id, offers.category, offers.title, offers.description, offers.image_url, offers.whatsapp,
                    offers.location, offers.lat, offers.lon, offers.status, offers.created_at, offers.expires_at,
                    users.business_name, users.email
             FROM offers
             INNER JOIN users ON users.id = offers.user_id
             ORDER BY CASE offers.status
                    WHEN 'pending' THEN 0
                    WHEN 'active' THEN 1
                    WHEN 'rejected' THEN 2
                    WHEN 'expired' THEN 3
                    ELSE 4 END,
                    datetime(offers.created_at) DESC"
        );

        return $statement->fetchAll();
    }

    public function updateStatus(int $offerId, string $status): void
    {
        $statement = $this->pdo->prepare('UPDATE offers SET status = :status WHERE id = :id');
        $statement->execute([
            'status' => $status,
            'id' => $offerId,
        ]);
    }

    public function expireOffers(): int
    {
        $statement = $this->pdo->prepare(
            "UPDATE offers
             SET status = 'expired'
             WHERE status IN ('pending', 'active')
               AND datetime(expires_at) <= datetime('now')"
        );
        $statement->execute();

        return $statement->rowCount();
    }
}
