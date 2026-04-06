<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\BusinessTemplate;

use App\Domain\BusinessTemplate\BusinessTemplateRepository;
use PDO;

class SqliteBusinessTemplateRepository implements BusinessTemplateRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAllActive(): array
    {
        $query = <<<SQL
            SELECT id, key, name, icon, description, fields_json, display_order
            FROM business_templates
            WHERE is_active = 1
            ORDER BY display_order ASC, name ASC
        SQL;

        $statement = $this->connection->prepare($query);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findByKey(string $key): ?array
    {
        $query = <<<SQL
            SELECT id, key, name, icon, description, fields_json, is_active, display_order, created_at, updated_at
            FROM business_templates
            WHERE key = ?
            LIMIT 1
        SQL;

        $statement = $this->connection->prepare($query);
        $statement->execute([$key]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function create(array $data): array
    {
        $query = <<<SQL
            INSERT INTO business_templates (key, name, icon, description, fields_json, is_active, display_order)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        SQL;

        $statement = $this->connection->prepare($query);
        $statement->execute([
            $data['key'] ?? '',
            $data['name'] ?? '',
            $data['icon'] ?? null,
            $data['description'] ?? null,
            isset($data['fields_json']) ? json_encode($data['fields_json']) : '[]',
            $data['is_active'] ?? 1,
            $data['display_order'] ?? 0,
        ]);

        $id = (int) $this->connection->lastInsertId();

        return $this->findByKey($data['key'] ?? '') ?? ['id' => $id];
    }

    public function update(string $key, array $data): ?array
    {
        $updates = [];
        $values = [];

        if (isset($data['name'])) {
            $updates[] = 'name = ?';
            $values[] = $data['name'];
        }

        if (isset($data['icon'])) {
            $updates[] = 'icon = ?';
            $values[] = $data['icon'];
        }

        if (isset($data['description'])) {
            $updates[] = 'description = ?';
            $values[] = $data['description'];
        }

        if (isset($data['fields_json'])) {
            $updates[] = 'fields_json = ?';
            $values[] = json_encode($data['fields_json']);
        }

        if (isset($data['is_active'])) {
            $updates[] = 'is_active = ?';
            $values[] = $data['is_active'] ? 1 : 0;
        }

        if (isset($data['display_order'])) {
            $updates[] = 'display_order = ?';
            $values[] = $data['display_order'];
        }

        if (empty($updates)) {
            return $this->findByKey($key);
        }

        $updates[] = 'updated_at = CURRENT_TIMESTAMP';
        $values[] = $key;

        $query = 'UPDATE business_templates SET ' . implode(', ', $updates) . ' WHERE key = ? RETURNING *';

        $statement = $this->connection->prepare($query);
        $statement->execute($values);

        return $statement->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function delete(string $key): bool
    {
        $query = 'DELETE FROM business_templates WHERE key = ?';

        $statement = $this->connection->prepare($query);

        return $statement->execute([$key]);
    }
}
