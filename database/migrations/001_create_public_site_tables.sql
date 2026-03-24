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
    (2, 'panaderia@barrio.test', '$2y$12$nhQstU3bHuRg/PuxuHEZE.Z0S8ntc.gu17XL1w48Na2AQEuJcC0D.', 'business', 'Panadería del Barrio', '+54 9 11 1234 5678', '2026-03-23 09:00:00'),
    (3, 'deportes@centro.test', '$2y$12$Ely.rCI6kJgxXeLnlZlpRe00S0ZXSQJR0aQKGpnJY74hv4AjQA9mK', 'business', 'Deportes Centro', '+54 9 11 2222 3333', '2026-03-23 10:30:00'),
    (4, 'visitante@ofertascerca.test', '$2y$12$ja9qkoTw7XzN0I70tyoPBu8AwFooQ6MJrhnJ3hL4l1rud0MDP5rPO', 'user', NULL, NULL, '2026-03-23 11:00:00');

INSERT OR IGNORE INTO offers (
    id,
    user_id,
    category,
    title,
    description,
    image_url,
    whatsapp,
    location,
    lat,
    lon,
    status,
    created_at,
    expires_at
) VALUES
    (
        1,
        2,
        'Gastronomía',
        'Combo desayuno con 25% de descuento',
        'Café, medialunas y jugo con retiro en el local.',
        'https://images.unsplash.com/photo-1504754524776-8f4f37790ca0?auto=format&fit=crop&w=900&q=80',
        '+54 9 11 1234 5678',
        'Av. Siempre Viva 123',
        -34.6037,
        -58.3816,
        'active',
        datetime('now', '-2 hours'),
        datetime(datetime('now', '-2 hours'), '+1 day')
    ),
    (
        2,
        3,
        'Deportes',
        'Zapatillas urbanas con envío local',
        'Talle limitado y retiro en el día para compras confirmadas.',
        'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=80',
        '+54 9 11 2222 3333',
        'Calle Central 456',
        -34.6010,
        -58.3772,
        'active',
        datetime('now', '-6 hours'),
        datetime(datetime('now', '-6 hours'), '+1 day')
    ),
    (
        3,
        2,
        'Panadería',
        'Docena de facturas recién horneadas',
        'Promoción válida hasta agotar stock con atención por WhatsApp.',
        'https://images.unsplash.com/photo-1517433670267-08bbd4be890f?auto=format&fit=crop&w=900&q=80',
        '+54 9 11 1234 5678',
        'Av. Siempre Viva 123',
        -34.6037,
        -58.3816,
        'pending',
        datetime('now', '-30 minutes'),
        datetime(datetime('now', '-30 minutes'), '+1 day')
    );
