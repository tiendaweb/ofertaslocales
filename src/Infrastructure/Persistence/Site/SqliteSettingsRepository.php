<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Site;

use App\Domain\Site\SettingsRepository;
use PDO;

class SqliteSettingsRepository implements SettingsRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findAll(): array
    {
        $statement = $this->pdo->query('SELECT key, value FROM settings ORDER BY key ASC');
        $settings = [];

        foreach ($statement->fetchAll() as $row) {
            $settings[(string) $row['key']] = (string) $row['value'];
        }

        return $settings;
    }

    public function findByKeys(array $keys): array
    {
        if ($keys === []) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($keys), '?'));
        $statement = $this->pdo->prepare(
            sprintf('SELECT key, value FROM settings WHERE key IN (%s) ORDER BY key ASC', $placeholders)
        );
        $statement->execute(array_values($keys));

        $settings = [];
        foreach ($statement->fetchAll() as $row) {
            $settings[(string) $row['key']] = (string) $row['value'];
        }

        return $settings;
    }
}
