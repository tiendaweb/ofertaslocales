-- Migration: Add Legal Pages (Terms, Privacy, Cookies)
-- Adds support for administrable legal pages with audit trail

-- Table: legal_pages
CREATE TABLE IF NOT EXISTS legal_pages (
    page_key TEXT PRIMARY KEY,
    title TEXT NOT NULL,
    content_html TEXT NOT NULL,
    last_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by_user_id INTEGER REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: user_policy_acceptance (audit trail)
CREATE TABLE IF NOT EXISTS user_policy_acceptance (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER REFERENCES users(id),
    policy_type TEXT NOT NULL CHECK(policy_type IN ('terms', 'privacy', 'cookies')),
    accepted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes
CREATE INDEX idx_legal_pages_key ON legal_pages(page_key);
CREATE INDEX idx_user_policy_acceptance_user ON user_policy_acceptance(user_id);
CREATE INDEX idx_user_policy_acceptance_type ON user_policy_acceptance(policy_type);
CREATE INDEX idx_user_policy_acceptance_date ON user_policy_acceptance(accepted_at);

-- Insert default legal pages
INSERT INTO legal_pages (page_key, title, content_html, created_at)
VALUES
    ('terms', 'Términos y Condiciones', '<h1>Términos y Condiciones</h1><p>Los términos y condiciones se actualizarán pronto.</p>', CURRENT_TIMESTAMP),
    ('privacy', 'Política de Privacidad', '<h1>Política de Privacidad</h1><p>Nuestra política de privacidad se actualizará pronto.</p>', CURRENT_TIMESTAMP),
    ('cookies', 'Política de Cookies', '<h1>Política de Cookies</h1><p>Usamos cookies para mejorar tu experiencia.</p>', CURRENT_TIMESTAMP);
