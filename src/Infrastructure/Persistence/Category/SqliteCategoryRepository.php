<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Category;

use App\Domain\Category\CategoryRepository;
use PDO;

class SqliteCategoryRepository implements CategoryRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findApprovedNames(): array
    {
        $statement = $this->pdo->query(
            "SELECT name FROM offer_categories WHERE status = 'approved' ORDER BY name ASC"
        );

        return array_values(array_map(
            static fn (mixed $name): string => (string) $name,
            $statement->fetchAll(PDO::FETCH_COLUMN)
        ));
    }

    public function findAll(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, name, status, requested_by_user_id, created_at, reviewed_at, reviewed_by_user_id
             FROM offer_categories
             ORDER BY status ASC, name ASC'
        );

        return $statement->fetchAll();
    }

    public function findPending(): array
    {
        $statement = $this->pdo->query(
            "SELECT id, name, status, requested_by_user_id, created_at FROM offer_categories WHERE status = 'pending' ORDER BY created_at ASC"
        );

        return $statement->fetchAll();
    }

    public function createApproved(string $name, int $adminUserId): bool
    {
        return $this->upsertCategory($name, 'approved', null, $adminUserId);
    }

    public function requestCategory(string $name, int $requestedByUserId): bool
    {
        return $this->upsertCategory($name, 'pending', $requestedByUserId, null);
    }

    public function updateStatus(int $categoryId, string $status, int $adminUserId): bool
    {
        if (!in_array($status, ['approved', 'rejected'], true)) {
            return false;
        }

        $statement = $this->pdo->prepare(
            'UPDATE offer_categories
             SET status = :status,
                 reviewed_at = :reviewed_at,
                 reviewed_by_user_id = :reviewed_by_user_id
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $categoryId,
            'status' => $status,
            'reviewed_at' => gmdate('Y-m-d H:i:s'),
            'reviewed_by_user_id' => $adminUserId,
        ]);
    }


    public function updateName(int $id, string $name, int $adminId): bool
    {
        $trimmedName = trim($name);
        if ($id <= 0 || $trimmedName === '') {
            return false;
        }

        $normalizedName = $this->normalize($trimmedName);
        $conflict = $this->pdo->prepare(
            'SELECT 1 FROM offer_categories WHERE normalized_name = :normalized_name AND id <> :id LIMIT 1'
        );
        $conflict->execute([
            'normalized_name' => $normalizedName,
            'id' => $id,
        ]);

        if ($conflict->fetchColumn() !== false) {
            return false;
        }

        $statement = $this->pdo->prepare(
            'UPDATE offer_categories
             SET name = :name,
                 normalized_name = :normalized_name,
                 reviewed_at = :reviewed_at,
                 reviewed_by_user_id = :reviewed_by_user_id
             WHERE id = :id'
        );
        $statement->execute([
            'id' => $id,
            'name' => $trimmedName,
            'normalized_name' => $normalizedName,
            'reviewed_at' => gmdate('Y-m-d H:i:s'),
            'reviewed_by_user_id' => $adminId,
        ]);

        return $statement->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        if ($id <= 0) {
            return false;
        }

        $statement = $this->pdo->prepare('DELETE FROM offer_categories WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $statement->rowCount() > 0;
    }

    public function isApproved(string $name): bool
    {
        $statement = $this->pdo->prepare(
            "SELECT 1 FROM offer_categories WHERE normalized_name = :normalized_name AND status = 'approved' LIMIT 1"
        );
        $statement->execute(['normalized_name' => $this->normalize($name)]);

        return $statement->fetchColumn() !== false;
    }

    private function upsertCategory(string $name, string $status, ?int $requestedByUserId, ?int $reviewedByUserId): bool
    {
        $trimmedName = trim($name);
        if ($trimmedName === '') {
            return false;
        }

        $normalizedName = $this->normalize($trimmedName);

        $existing = $this->pdo->prepare('SELECT id, status FROM offer_categories WHERE normalized_name = :normalized_name LIMIT 1');
        $existing->execute(['normalized_name' => $normalizedName]);
        $row = $existing->fetch();

        if ($row === false) {
            $statement = $this->pdo->prepare(
                'INSERT INTO offer_categories (name, normalized_name, status, requested_by_user_id, reviewed_at, reviewed_by_user_id, created_at)
                 VALUES (:name, :normalized_name, :status, :requested_by_user_id, :reviewed_at, :reviewed_by_user_id, :created_at)'
            );

            return $statement->execute([
                'name' => $trimmedName,
                'normalized_name' => $normalizedName,
                'status' => $status,
                'requested_by_user_id' => $requestedByUserId,
                'reviewed_at' => $status === 'approved' ? gmdate('Y-m-d H:i:s') : null,
                'reviewed_by_user_id' => $reviewedByUserId,
                'created_at' => gmdate('Y-m-d H:i:s'),
            ]);
        }

        if ((string) $row['status'] === 'approved' && $status === 'pending') {
            return true;
        }

        $statement = $this->pdo->prepare(
            'UPDATE offer_categories
             SET name = :name,
                 status = :status,
                 requested_by_user_id = :requested_by_user_id,
                 reviewed_at = :reviewed_at,
                 reviewed_by_user_id = :reviewed_by_user_id
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => (int) $row['id'],
            'name' => $trimmedName,
            'status' => $status,
            'requested_by_user_id' => $requestedByUserId,
            'reviewed_at' => $status === 'approved' ? gmdate('Y-m-d H:i:s') : null,
            'reviewed_by_user_id' => $reviewedByUserId,
        ]);
    }

    private function normalize(string $value): string
    {
        return mb_strtolower(trim($value));
    }
}
