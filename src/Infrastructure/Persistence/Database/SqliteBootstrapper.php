<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Database;

use PDO;
use RuntimeException;

class SqliteBootstrapper
{
    public function __construct(private readonly string $schemaPath)
    {
    }

    public function bootstrap(PDO $pdo): void
    {
        $requiredTables = ['users', 'offers', 'settings', 'seo'];
        $existingTables = $pdo->query(
            "SELECT name FROM sqlite_master WHERE type = 'table'"
        )->fetchAll(PDO::FETCH_COLUMN);

        $missingTables = array_diff($requiredTables, $existingTables);
        if ($missingTables === []) {
            return;
        }

        if (!is_file($this->schemaPath)) {
            throw new RuntimeException('No se encontró el esquema SQLite inicial.');
        }

        $schema = file_get_contents($this->schemaPath);
        if ($schema === false) {
            throw new RuntimeException('No se pudo leer el esquema SQLite inicial.');
        }

        $pdo->exec($schema);
    }
}
