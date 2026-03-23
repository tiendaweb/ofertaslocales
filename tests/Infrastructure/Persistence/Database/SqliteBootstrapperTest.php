<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\Database;

use App\Infrastructure\Persistence\Database\SqliteBootstrapper;
use PDO;
use Tests\TestCase;

class SqliteBootstrapperTest extends TestCase
{
    public function testBootstrapCreatesExpectedTablesWhenDatabaseIsEmpty(): void
    {
        $databasePath = tempnam(sys_get_temp_dir(), 'ofertas-sqlite-');
        $schemaPath = tempnam(sys_get_temp_dir(), 'ofertas-schema-');

        $schema = implode("\n", [
            'CREATE TABLE users (',
            '    id INTEGER PRIMARY KEY AUTOINCREMENT,',
            '    email TEXT NOT NULL,',
            '    password TEXT NOT NULL,',
            '    role TEXT NOT NULL,',
            '    business_name TEXT,',
            '    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP',
            ');',
            'CREATE TABLE offers (',
            '    id INTEGER PRIMARY KEY AUTOINCREMENT,',
            '    user_id INTEGER NOT NULL,',
            '    category TEXT NOT NULL,',
            '    title TEXT NOT NULL,',
            '    description TEXT NOT NULL,',
            '    image_url TEXT,',
            '    whatsapp TEXT NOT NULL,',
            '    location TEXT NOT NULL,',
            '    lat REAL,',
            '    lon REAL,',
            '    status TEXT NOT NULL,',
            '    expires_at TEXT NOT NULL',
            ');',
            'CREATE TABLE settings (key TEXT PRIMARY KEY, value TEXT NOT NULL);',
            'CREATE TABLE seo (',
            '    page_name TEXT PRIMARY KEY,',
            '    title TEXT NOT NULL,',
            '    meta_description TEXT NOT NULL,',
            '    og_image TEXT',
            ');',
        ]);
        file_put_contents($schemaPath, $schema);

        $pdo = new PDO('sqlite:' . $databasePath);
        $bootstrapper = new SqliteBootstrapper($schemaPath);
        $bootstrapper->bootstrap($pdo);

        $tables = $pdo->query(
            "SELECT name FROM sqlite_master WHERE type = 'table' ORDER BY name ASC"
        )->fetchAll(PDO::FETCH_COLUMN);

        $this->assertContains('users', $tables);
        $this->assertContains('offers', $tables);
        $this->assertContains('settings', $tables);
        $this->assertContains('seo', $tables);

        @unlink($databasePath);
        @unlink($schemaPath);
    }
}
