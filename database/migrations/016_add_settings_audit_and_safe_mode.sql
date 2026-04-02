CREATE TABLE IF NOT EXISTS settings_audit_log (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    setting_key TEXT NOT NULL,
    old_value TEXT,
    new_value TEXT,
    changed_by_user_id INTEGER,
    changed_by_email TEXT,
    changed_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_settings_audit_log_key_changed_at
    ON settings_audit_log (setting_key, changed_at DESC);

INSERT INTO settings (key, value)
VALUES ('safe_mode', '0')
ON CONFLICT(key) DO NOTHING;
