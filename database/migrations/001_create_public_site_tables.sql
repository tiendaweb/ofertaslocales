PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT NOT NULL CHECK (role IN ('admin', 'business', 'user')),
    business_name TEXT,
    whatsapp TEXT,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS offers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    category TEXT NOT NULL,
    title TEXT NOT NULL,
    description TEXT NOT NULL,
    image_url TEXT,
    whatsapp TEXT NOT NULL,
    location TEXT NOT NULL,
    lat REAL,
    lon REAL,
    status TEXT NOT NULL CHECK (status IN ('pending', 'active', 'expired', 'rejected')) DEFAULT 'pending',
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at TEXT NOT NULL DEFAULT (datetime(CURRENT_TIMESTAMP, '+1 day')),
    FOREIGN KEY (user_id) REFERENCES users (id)
);

CREATE INDEX IF NOT EXISTS idx_offers_public_visibility
    ON offers (status, expires_at);

CREATE INDEX IF NOT EXISTS idx_offers_user_status
    ON offers (user_id, status, expires_at);

CREATE TABLE IF NOT EXISTS settings (
    key TEXT PRIMARY KEY,
    value TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS seo (
    page_name TEXT PRIMARY KEY,
    title TEXT NOT NULL,
    meta_description TEXT NOT NULL,
    og_image TEXT
);

INSERT OR IGNORE INTO settings (key, value) VALUES
    ('site_name', 'OfertasLocales'),
    ('site_logo_url', ''),
    ('hero_badge', '📍 Descubrí tu zona'),
    ('hero_title', 'Ofertas cerca tuyo que te hacen ahorrar HOY'),
    ('hero_description', 'Encontrá descuentos reales, contactá directo al vendedor por WhatsApp y asegurá tu precio antes de que el reloj llegue a cero.'),
    ('hero_primary_cta', 'Ver descuentos ahora'),
    ('merchant_badge', 'Para Comerciantes'),
    ('merchant_title', 'Conseguí más clientes hoy mismo'),
    ('merchant_description', 'Publicá tu oferta GRATIS por 24 horas y recibí consultas directas por WhatsApp.'),
    ('footer_tagline', 'Hecho con ❤️ para potenciar negocios de barrio.'),
    ('approval_mode', 'manual');

INSERT OR IGNORE INTO seo (page_name, title, meta_description, og_image) VALUES
    ('home', 'OfertasLocales | Inicio', 'Ofertas locales activas por 24 horas con contacto directo por WhatsApp.', '/uploads/og-home.png'),
    ('ofertas', 'OfertasLocales | Ofertas', 'Listado público de ofertas activas visibles solo mientras no expiren.', '/uploads/og-ofertas.png'),
    ('negocios', 'OfertasLocales | Negocios', 'Negocios con ofertas activas disponibles en este momento.', '/uploads/og-negocios.png'),
    ('mapa', 'OfertasLocales | Mapa', 'Mapa con negocios que tienen ofertas activas cerca tuyo.', '/uploads/og-mapa.png');

INSERT OR IGNORE INTO users (id, email, password, role, business_name, whatsapp, created_at) VALUES
    (1, 'admin@admin.com', '$2y$12$3RyD9WbPkEpMtUXPUI9dRuCJE1Bd2NMRnn3QzOkofdY1.jh0z8g8G', 'admin', 'OfertasLocales Admin', '+54 9 11 0000 0000', '2026-03-23 08:00:00'),
    (4, 'visitante@ofertascerca.test', '$2y$12$ja9qkoTw7XzN0I70tyoPBu8AwFooQ6MJrhnJ3hL4l1rud0MDP5rPO', 'user', NULL, NULL, '2026-03-23 11:00:00');
