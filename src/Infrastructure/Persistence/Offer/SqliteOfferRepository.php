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
            "SELECT offers.id, offers.category, offers.title, offers.description, COALESCE(offers.image_url, '') AS image_url, offers.whatsapp,
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

    public function deleteByAdmin(int $offerId): bool
    {
        $statement = $this->pdo->prepare('DELETE FROM offers WHERE id = :id');
        $statement->execute([
            'id' => $offerId,
        ]);

        return $statement->rowCount() > 0;
    }

    public function updateByAdmin(int $offerId, array $data): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE offers
             SET category = :category,
                 title = :title,
                 description = :description,
                 whatsapp = :whatsapp,
                 location = :location
             WHERE id = :id'
        );

        $statement->execute([
            'id' => $offerId,
            'category' => trim((string) $data['category']),
            'title' => trim((string) $data['title']),
            'description' => trim((string) $data['description']),
            'whatsapp' => trim((string) $data['whatsapp']),
            'location' => trim((string) $data['location']),
        ]);

        return $statement->rowCount() > 0;
    }


    public function updateForUser(int $offerId, int $userId, array $data): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE offers
             SET category = :category,
                 title = :title,
                 description = :description,
                 whatsapp = :whatsapp,
                 location = :location
             WHERE id = :id
               AND user_id = :user_id'
        );

        $statement->execute([
            'id' => $offerId,
            'user_id' => $userId,
            'category' => trim((string) $data['category']),
            'title' => trim((string) $data['title']),
            'description' => trim((string) $data['description']),
            'whatsapp' => trim((string) $data['whatsapp']),
            'location' => trim((string) $data['location']),
        ]);

        return $statement->rowCount() > 0;
    }

    public function updateStatusForUser(int $offerId, int $userId, string $status): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE offers
             SET status = :status
             WHERE id = :id
               AND user_id = :user_id'
        );
        $statement->execute([
            'id' => $offerId,
            'user_id' => $userId,
            'status' => $status,
        ]);

        if ($statement->rowCount() > 0) {
            return true;
        }

        $existsStatement = $this->pdo->prepare(
            'SELECT 1 FROM offers WHERE id = :id AND user_id = :user_id LIMIT 1'
        );
        $existsStatement->execute([
            'id' => $offerId,
            'user_id' => $userId,
        ]);

        return $existsStatement->fetchColumn() !== false;
    }

    public function duplicateForUser(int $offerId, int $userId): ?int
    {
        $source = $this->pdo->prepare(
            'SELECT category, title, description, image_url, whatsapp, location, lat, lon
             FROM offers
             WHERE id = :id AND user_id = :user_id'
        );
        $source->execute([
            'id' => $offerId,
            'user_id' => $userId,
        ]);

        $offer = $source->fetch();
        if (!is_array($offer)) {
            return null;
        }

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
            'category' => (string) $offer['category'],
            'title' => (string) $offer['title'],
            'description' => (string) $offer['description'],
            'image_url' => $offer['image_url'],
            'whatsapp' => (string) $offer['whatsapp'],
            'location' => (string) $offer['location'],
            'lat' => $offer['lat'],
            'lon' => $offer['lon'],
            'status' => 'pending',
            'created_at' => gmdate('Y-m-d H:i:s'),
            'expires_at' => gmdate('Y-m-d H:i:s', strtotime('+7 days')),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function softDeleteForUser(int $offerId, int $userId): bool
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM offers
             WHERE id = :id
               AND user_id = :user_id'
        );
        $statement->execute([
            'id' => $offerId,
            'user_id' => $userId,
        ]);

        return $statement->rowCount() > 0;
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
