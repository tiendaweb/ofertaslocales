<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\LegalPage;

use App\Domain\LegalPage\LegalPageRepository;
use PDO;

class SqliteLegalPageRepository implements LegalPageRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $query = <<<SQL
            SELECT page_key, title, content_html, last_updated_at, created_at
            FROM legal_pages
            ORDER BY page_key ASC
        SQL;

        $statement = $this->connection->prepare($query);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findByKey(string $key): ?array
    {
        $query = <<<SQL
            SELECT page_key, title, content_html, last_updated_at, created_at
            FROM legal_pages
            WHERE page_key = ?
            LIMIT 1
        SQL;

        $statement = $this->connection->prepare($query);
        $statement->execute([$key]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function update(string $key, string $title, string $contentHtml, ?int $userId = null): ?array
    {
        // Use UPSERT (INSERT OR REPLACE) for SQLite
        $query = <<<SQL
            INSERT INTO legal_pages (page_key, title, content_html, updated_by_user_id, last_updated_at)
            VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
            ON CONFLICT(page_key) DO UPDATE SET
                title = excluded.title,
                content_html = excluded.content_html,
                updated_by_user_id = excluded.updated_by_user_id,
                last_updated_at = CURRENT_TIMESTAMP
        SQL;

        $statement = $this->connection->prepare($query);
        $statement->execute([$key, $title, $contentHtml, $userId]);

        return $this->findByKey($key);
    }

    public function recordAcceptance(int $userId, string $policyType, ?string $ipAddress = null): bool
    {
        // Check if already accepted (no need to record twice)
        if ($this->hasAccepted($userId, $policyType)) {
            return true;
        }

        $query = <<<SQL
            INSERT INTO user_policy_acceptance (user_id, policy_type, ip_address)
            VALUES (?, ?, ?)
        SQL;

        $statement = $this->connection->prepare($query);

        return $statement->execute([$userId, $policyType, $ipAddress]);
    }

    public function hasAccepted(int $userId, string $policyType): bool
    {
        $query = <<<SQL
            SELECT 1 FROM user_policy_acceptance
            WHERE user_id = ? AND policy_type = ?
            LIMIT 1
        SQL;

        $statement = $this->connection->prepare($query);
        $statement->execute([$userId, $policyType]);

        return $statement->fetch() !== false;
    }
}
