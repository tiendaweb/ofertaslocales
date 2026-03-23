<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Database;

use PDO;
use RuntimeException;
use Throwable;

class SqliteBootstrapper
{
    public function __construct(private readonly string $migrationsPath)
    {
    }

    public function bootstrap(PDO $pdo): void
    {
        $this->ensureMigrationsTable($pdo);

        foreach ($this->resolveMigrationFiles() as $migrationFile) {
            $migrationName = basename($migrationFile);

            if ($this->hasMigrationBeenApplied($pdo, $migrationName)) {
                continue;
            }

            $migrationSql = file_get_contents($migrationFile);
            if ($migrationSql === false) {
                throw new RuntimeException(sprintf('No se pudo leer la migración SQLite "%s".', $migrationName));
            }

            $this->applyMigration($pdo, $migrationName, $migrationSql);
        }

        $this->assertRequiredTables($pdo);
    }

    private function ensureMigrationsTable(PDO $pdo): void
    {
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS schema_migrations (
                name TEXT PRIMARY KEY,
                applied_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )'
        );
    }

    private function resolveMigrationFiles(): array
    {
        if (!is_dir($this->migrationsPath)) {
            throw new RuntimeException('No se encontró el directorio de migraciones SQLite.');
        }

        $migrationFiles = glob($this->migrationsPath . '/*.sql');
        if ($migrationFiles === false || $migrationFiles === []) {
            throw new RuntimeException('No se encontraron migraciones SQLite para inicializar la base de datos.');
        }

        sort($migrationFiles);

        return $migrationFiles;
    }

    private function hasMigrationBeenApplied(PDO $pdo, string $migrationName): bool
    {
        $statement = $pdo->prepare('SELECT 1 FROM schema_migrations WHERE name = :name LIMIT 1');
        $statement->execute(['name' => $migrationName]);

        return $statement->fetchColumn() !== false;
    }

    private function applyMigration(PDO $pdo, string $migrationName, string $migrationSql): void
    {
        $pdo->beginTransaction();

        try {
            $pdo->exec($migrationSql);

            $statement = $pdo->prepare(
                'INSERT INTO schema_migrations (name, applied_at) VALUES (:name, CURRENT_TIMESTAMP)'
            );
            $statement->execute(['name' => $migrationName]);

            $pdo->commit();
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw new RuntimeException(
                sprintf('Falló la aplicación de la migración SQLite "%s".', $migrationName),
                0,
                $exception
            );
        }
    }

    private function assertRequiredTables(PDO $pdo): void
    {
        $requiredTables = ['users', 'offers', 'settings', 'seo'];
        $existingTables = $pdo->query(
            "SELECT name FROM sqlite_master WHERE type = 'table'"
        )->fetchAll(PDO::FETCH_COLUMN);

        $missingTables = array_diff($requiredTables, $existingTables);
        if ($missingTables !== []) {
            throw new RuntimeException(
                sprintf('Faltan tablas obligatorias después de las migraciones: %s.', implode(', ', $missingTables))
            );
        }
    }
}
