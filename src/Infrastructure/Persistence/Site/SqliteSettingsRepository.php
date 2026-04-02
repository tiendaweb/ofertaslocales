<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Site;

use App\Domain\Site\SettingsRepository;
use PDO;

class SqliteSettingsRepository implements SettingsRepository
{
    private const AUDITED_SETTING_KEYS = [
        'contact_whatsapp',
        'site_name',
        'maintenance_mode',
        'custom_css_frontend',
        'custom_js_frontend',
        'custom_css_panel',
        'custom_js_panel',
    ];

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

    public function updateMany(array $settings, array $auditContext = []): void
    {
        if ($settings === []) {
            return;
        }

        $existingValues = $this->findByKeys(array_keys($settings));
        $statement = $this->pdo->prepare(
            'INSERT INTO settings (key, value) VALUES (:key, :value)
             ON CONFLICT(key) DO UPDATE SET value = excluded.value'
        );

        foreach ($settings as $key => $value) {
            $statement->execute([
                'key' => (string) $key,
                'value' => (string) $value,
            ]);

            $this->storeAuditLog((string) $key, $existingValues[(string) $key] ?? null, (string) $value, $auditContext);
        }
    }

    public function findAuditLogs(array $keys, int $limit = 50): array
    {
        if ($keys === []) {
            return [];
        }

        $safeLimit = max(1, min(200, $limit));
        $placeholders = implode(', ', array_fill(0, count($keys), '?'));
        $statement = $this->pdo->prepare(
            sprintf(
                'SELECT setting_key, old_value, new_value, changed_by_user_id, changed_by_email, changed_at
                 FROM settings_audit_log
                 WHERE setting_key IN (%s)
                 ORDER BY changed_at DESC, id DESC
                 LIMIT %d',
                $placeholders,
                $safeLimit
            )
        );
        $statement->execute(array_values($keys));

        return $statement->fetchAll() ?: [];
    }

    private function storeAuditLog(string $key, ?string $oldValue, string $newValue, array $auditContext): void
    {
        if (!in_array($key, self::AUDITED_SETTING_KEYS, true)) {
            return;
        }

        if ($oldValue !== null && $oldValue === $newValue) {
            return;
        }

        $statement = $this->pdo->prepare(
            'INSERT INTO settings_audit_log
                (setting_key, old_value, new_value, changed_by_user_id, changed_by_email, changed_at)
             VALUES (:setting_key, :old_value, :new_value, :changed_by_user_id, :changed_by_email, CURRENT_TIMESTAMP)'
        );

        $statement->execute([
            'setting_key' => $key,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'changed_by_user_id' => $auditContext['user_id'] ?? null,
            'changed_by_email' => $auditContext['email'] ?? null,
        ]);
    }
}
