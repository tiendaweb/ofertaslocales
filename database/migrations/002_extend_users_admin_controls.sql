ALTER TABLE users ADD COLUMN status TEXT NOT NULL DEFAULT 'active' CHECK (status IN ('active', 'suspended'));
ALTER TABLE users ADD COLUMN is_suspended INTEGER NOT NULL DEFAULT 0 CHECK (is_suspended IN (0, 1));
ALTER TABLE users ADD COLUMN suspended_at TEXT;
ALTER TABLE users ADD COLUMN suspended_reason TEXT;
ALTER TABLE users ADD COLUMN suspended_by INTEGER;
ALTER TABLE users ADD COLUMN updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE users ADD COLUMN last_login_at TEXT;

UPDATE users
SET status = CASE WHEN role = 'admin' OR role = 'business' OR role = 'user' THEN 'active' ELSE status END,
    is_suspended = 0,
    updated_at = COALESCE(updated_at, created_at, CURRENT_TIMESTAMP)
WHERE status IS NULL OR is_suspended IS NULL OR updated_at IS NULL;

CREATE INDEX IF NOT EXISTS idx_users_status_role ON users (status, role);
