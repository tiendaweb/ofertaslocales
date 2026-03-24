CREATE TABLE IF NOT EXISTS offer_categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    normalized_name TEXT NOT NULL UNIQUE,
    status TEXT NOT NULL CHECK (status IN ('approved', 'pending', 'rejected')) DEFAULT 'pending',
    requested_by_user_id INTEGER,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TEXT,
    reviewed_by_user_id INTEGER,
    FOREIGN KEY (requested_by_user_id) REFERENCES users (id),
    FOREIGN KEY (reviewed_by_user_id) REFERENCES users (id)
);

CREATE INDEX IF NOT EXISTS idx_offer_categories_status ON offer_categories (status, name);

INSERT OR IGNORE INTO offer_categories (name, normalized_name, status, created_at)
VALUES
    ('Gastronomía', lower(trim('Gastronomía')), 'approved', CURRENT_TIMESTAMP),
    ('Deportes', lower(trim('Deportes')), 'approved', CURRENT_TIMESTAMP),
    ('Panadería', lower(trim('Panadería')), 'approved', CURRENT_TIMESTAMP);

INSERT OR IGNORE INTO offer_categories (name, normalized_name, status, created_at)
SELECT DISTINCT category, lower(trim(category)), 'approved', CURRENT_TIMESTAMP
FROM offers
WHERE trim(category) <> '';
