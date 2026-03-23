PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS offers;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS seo;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT NOT NULL CHECK (role IN ('admin', 'business', 'user')),
    business_name TEXT,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE offers (
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
    status TEXT NOT NULL CHECK (status IN ('pending', 'active', 'expired')) DEFAULT 'pending',
    expires_at TEXT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users (id)
);

CREATE TABLE settings (
    key TEXT PRIMARY KEY,
    value TEXT NOT NULL
);

CREATE TABLE seo (
    page_name TEXT PRIMARY KEY,
    title TEXT NOT NULL,
    meta_description TEXT NOT NULL,
    og_image TEXT
);

INSERT INTO users (email, password, role, business_name) VALUES
    ('admin@ofertascerca.test', '$2y$10$adminDemo', 'admin', 'OfertasCerca Admin'),
    ('panaderia@barrio.test', '$2y$10$panaderiaDemo', 'business', 'Panadería del Barrio'),
    ('deportes@centro.test', '$2y$10$deportesDemo', 'business', 'Deportes Centro'),
    ('visitante@ofertascerca.test', '$2y$10$usuarioDemo', 'user', NULL);

INSERT INTO offers (user_id, category, title, description, image_url, whatsapp, location, lat, lon, status, expires_at) VALUES
    (2, 'Gastronomía', 'Combo desayuno con 25% de descuento', 'Café, medialunas y jugo con retiro en el local.', 'https://images.unsplash.com/photo-1504754524776-8f4f37790ca0?auto=format&fit=crop&w=900&q=80', '+54 9 11 1234 5678', 'Av. Siempre Viva 123', -34.6037, -58.3816, 'active', '2026-03-24 09:00:00'),
    (3, 'Deportes', 'Zapatillas urbanas con envío local', 'Talle limitado y retiro en el día para compras confirmadas.', 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=80', '+54 9 11 2222 3333', 'Calle Central 456', -34.6010, -58.3772, 'active', '2026-03-24 18:30:00'),
    (2, 'Panadería', 'Docena de facturas recién horneadas', 'Promoción válida hasta agotar stock con atención por WhatsApp.', 'https://images.unsplash.com/photo-1517433670267-08bbd4be890f?auto=format&fit=crop&w=900&q=80', '+54 9 11 1234 5678', 'Av. Siempre Viva 123', -34.6037, -58.3816, 'pending', '2026-03-25 07:30:00');

INSERT INTO settings (key, value) VALUES
    ('site_name', 'OfertasCerca'),
    ('hero_title', 'Encontrá ofertas locales cerca tuyo'),
    ('approval_mode', 'manual');

INSERT INTO seo (page_name, title, meta_description, og_image) VALUES
    ('home', 'OfertasCerca | Inicio', 'Ofertas locales activas y directorio de negocios.', '/uploads/og-home.png'),
    ('ofertas', 'OfertasCerca | Ofertas', 'Listado dinámico de promociones activas.', '/uploads/og-ofertas.png'),
    ('negocios', 'OfertasCerca | Negocios', 'Directorio de comercios registrados.', '/uploads/og-negocios.png');
