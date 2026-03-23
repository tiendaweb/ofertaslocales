<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\Database;

use App\Infrastructure\Persistence\Database\SqliteBootstrapper;
use PDO;
use Tests\TestCase;

class SqliteBootstrapperTest extends TestCase
{
    public function testBootstrapRunsMigrationsAndCreatesExpectedSchema(): void
    {
        $databasePath = tempnam(sys_get_temp_dir(), 'ofertas-sqlite-');
        $migrationsPath = sys_get_temp_dir() . '/ofertas-migrations-' . uniqid('', true);
        mkdir($migrationsPath);

        $migrationSql = implode("\n", [
            'CREATE TABLE example_settings (id INTEGER PRIMARY KEY AUTOINCREMENT, value TEXT NOT NULL);',
            'CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, whatsapp TEXT);',
            'CREATE TABLE offers (id INTEGER PRIMARY KEY AUTOINCREMENT, created_at TEXT, expires_at TEXT);',
            'CREATE TABLE settings (key TEXT PRIMARY KEY, value TEXT NOT NULL);',
            'CREATE TABLE seo (',
            '    page_name TEXT PRIMARY KEY,',
            '    title TEXT NOT NULL,',
            '    meta_description TEXT NOT NULL,',
            '    og_image TEXT',
            ');',
        ]);
        file_put_contents($migrationsPath . '/001_test.sql', $migrationSql);

        $pdo = new PDO('sqlite:' . $databasePath);
        $bootstrapper = new SqliteBootstrapper($migrationsPath);
        $bootstrapper->bootstrap($pdo);

        $tables = $pdo->query(
            "SELECT name FROM sqlite_master WHERE type = 'table' ORDER BY name ASC"
        )->fetchAll(PDO::FETCH_COLUMN);

        $this->assertContains('schema_migrations', $tables);
        $this->assertContains('users', $tables);
        $this->assertContains('offers', $tables);
        $this->assertContains('settings', $tables);
        $this->assertContains('seo', $tables);

        $appliedMigrations = $pdo->query(
            'SELECT name FROM schema_migrations ORDER BY name ASC'
        )->fetchAll(PDO::FETCH_COLUMN);
        $this->assertSame(['001_test.sql'], $appliedMigrations);

        @unlink($databasePath);
        @unlink($migrationsPath . '/001_test.sql');
        @rmdir($migrationsPath);
    }
}
