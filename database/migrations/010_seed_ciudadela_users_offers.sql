PRAGMA foreign_keys = ON;

-- Usuarios comunes (4)
INSERT OR IGNORE INTO users (
    email, password, role, business_name, whatsapp, street, street_number, postal_code, city, municipality, province, address_lat, address_lon, status, created_at, updated_at
) VALUES
    ('lucia.vecina@ciudadela.test', '$2y$12$ja9qkoTw7XzN0I70tyoPBu8AwFooQ6MJrhnJ3hL4l1rud0MDP5rPO', 'user', NULL, '+54 9 11 6000 0001', 'Av. Rivadavia', '12500', '1702', 'Ciudadela', 'Tres de Febrero', 'Buenos Aires', -34.640800, -58.536900, 'active', datetime('now'), datetime('now')),
    ('martin.ahorro@ciudadela.test', '$2y$12$ja9qkoTw7XzN0I70tyoPBu8AwFooQ6MJrhnJ3hL4l1rud0MDP5rPO', 'user', NULL, '+54 9 11 6000 0002', 'Calle Padre Elizalde', '980', '1702', 'Ciudadela', 'Tres de Febrero', 'Buenos Aires', -34.641300, -58.534800, 'active', datetime('now'), datetime('now')),
    ('paula.barrio@ciudadela.test', '$2y$12$ja9qkoTw7XzN0I70tyoPBu8AwFooQ6MJrhnJ3hL4l1rud0MDP5rPO', 'user', NULL, '+54 9 11 6000 0003', 'Av. Gaona', '3400', '1702', 'Ciudadela', 'Tres de Febrero', 'Buenos Aires', -34.639900, -58.540200, 'active', datetime('now'), datetime('now')),
    ('diego.zonaoeste@ciudadela.test', '$2y$12$ja9qkoTw7XzN0I70tyoPBu8AwFooQ6MJrhnJ3hL4l1rud0MDP5rPO', 'user', NULL, '+54 9 11 6000 0004', 'Av. Díaz Vélez', '450', '1702', 'Ciudadela', 'Tres de Febrero', 'Buenos Aires', -34.642200, -58.538400, 'active', datetime('now'), datetime('now'));

-- Negocios (6)
INSERT OR IGNORE INTO users (
    email, password, role, business_name, whatsapp, bio, street, street_number, postal_code, city, municipality, province, address_lat, address_lon, logo_url, cover_url, status, created_at, updated_at
) VALUES
    ('almacen.mayorista@ciudadela.test', '$2y$12$nhQstU3bHuRg/PuxuHEZE.Z0S8ntc.gu17XL1w48Na2AQEuJcC0D.', 'business', 'Almacén Mayorista Ciudadela', '+54 9 11 7000 0001', 'Ofertas semanales en productos de almacén.', 'Av. Rivadavia', '12780', '1702', 'Ciudadela', 'Tres de Febrero', 'Buenos Aires', -34.640500, -58.535700, 'https://images.unsplash.com/photo-1584473457409-ceb4f50ea4c8?auto=format&fit=crop&w=300&q=80', 'https://images.unsplash.com/photo-1579113800032-c38bd7635818?auto=format&fit=crop&w=1200&q=80', 'active', datetime('now'), datetime('now')),
    ('panaderia.esquina@ciudadela.test', '$2y$12$nhQstU3bHuRg/PuxuHEZE.Z0S8ntc.gu17XL1w48Na2AQEuJcC0D.', 'business', 'Panadería La Esquina', '+54 9 11 7000 0002', 'Facturas y pan recién horneado todo el día.', 'Calle Brandsen', '210', '1702', 'Ciudadela', 'Tres de Febrero', 'Buenos Aires', -34.641000, -58.537900, 'https://images.unsplash.com/photo-1608198093002-ad4e005484ec?auto=format&fit=crop&w=300&q=80', 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=1200&q=80', 'active', datetime('now'), datetime('now')),
    ('farmacia.bienestar@ciudadela.test', '$2y$12$nhQstU3bHuRg/PuxuHEZE.Z0S8ntc.gu17XL1w48Na2AQEuJcC0D.', 'business', 'Farmacia Bienestar', '+54 9 11 7000 0003', 'Descuentos en perfumería y cuidado personal.', 'Av. Gaona', '3550', '1702', 'Ciudadela', 'Tres de Febrero', 'Buenos Aires', -34.639700, -58.539100, 'https://images.unsplash.com/photo-1587854692152-cbe660dbde88?auto=format&fit=crop&w=300&q=80', 'https://images.unsplash.com/photo-1471864190281-a93a3070b6de?auto=format&fit=crop&w=1200&q=80', 'active', datetime('now'), datetime('now')),
    ('ferreteria.hogar@ciudadela.test', '$2y$12$nhQstU3bHuRg/PuxuHEZE.Z0S8ntc.gu17XL1w48Na2AQEuJcC0D.', 'business', 'Ferretería Hogar', '+54 9 11 7000 0004', 'Herramientas y accesorios para el hogar.', 'Calle Alianza', '145', '1702', 'Ciudadela', 'Tres de Febrero', 'Buenos Aires', -34.642500, -58.536100, 'https://images.unsplash.com/photo-1581147036324-c1c0f1f0d7f0?auto=format&fit=crop&w=300&q=80', 'https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?auto=format&fit=crop&w=1200&q=80', 'active', datetime('now'), datetime('now')),
    ('deportes.activos@ciudadela.test', '$2y$12$nhQstU3bHuRg/PuxuHEZE.Z0S8ntc.gu17XL1w48Na2AQEuJcC0D.', 'business', 'Deportes Activos', '+54 9 11 7000 0005', 'Indumentaria y calzado deportivo.', 'Av. Díaz Vélez', '600', '1702', 'Ciudadela', 'Tres de Febrero', 'Buenos Aires', -34.641800, -58.540000, 'https://images.unsplash.com/photo-1518459031867-a89b944bffe4?auto=format&fit=crop&w=300&q=80', 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=1200&q=80', 'active', datetime('now'), datetime('now')),
    ('kiosco.24hs@ciudadela.test', '$2y$12$nhQstU3bHuRg/PuxuHEZE.Z0S8ntc.gu17XL1w48Na2AQEuJcC0D.', 'business', 'Kiosco 24hs Centro', '+54 9 11 7000 0006', 'Promos rápidas para todos los días.', 'Av. Rivadavia', '12640', '1702', 'Ciudadela', 'Tres de Febrero', 'Buenos Aires', -34.640100, -58.538700, 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=300&q=80', 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=1200&q=80', 'active', datetime('now'), datetime('now'));

-- Ofertas: entre 1 y 3 por negocio (12 ofertas en total)
INSERT INTO offers (user_id, category, title, description, image_url, whatsapp, location, lat, lon, status, created_at, expires_at)
SELECT u.id, data.category, data.title, data.description, data.image_url, u.whatsapp,
       u.street || ' ' || u.street_number || ', ' || u.city,
       u.address_lat, u.address_lon,
       'active', datetime('now', '-1 hour'), datetime('now', '+1 day')
FROM users u
JOIN (
    SELECT 'almacen.mayorista@ciudadela.test' AS email, 'Almacén' AS category, '2x1 en fideos seleccionados' AS title, 'Llevando dos paquetes pagas uno en marcas participantes.' AS description, 'https://images.unsplash.com/photo-1586201375761-83865001e31b?auto=format&fit=crop&w=900&q=80' AS image_url
    UNION ALL SELECT 'almacen.mayorista@ciudadela.test', 'Bebidas', 'Gaseosa 2.25L con 20% OFF', 'Promo válida abonando en efectivo.', 'https://images.unsplash.com/photo-1581006852262-e4307cf6283a?auto=format&fit=crop&w=900&q=80'
    UNION ALL SELECT 'panaderia.esquina@ciudadela.test', 'Panadería', 'Docena de facturas al precio de 10', 'Retiro en sucursal durante la mañana.', 'https://images.unsplash.com/photo-1517433670267-08bbd4be890f?auto=format&fit=crop&w=900&q=80'
    UNION ALL SELECT 'panaderia.esquina@ciudadela.test', 'Panadería', 'Combo merienda con 25% OFF', 'Incluye café + 2 medialunas.', 'https://images.unsplash.com/photo-1504754524776-8f4f37790ca0?auto=format&fit=crop&w=900&q=80'
    UNION ALL SELECT 'farmacia.bienestar@ciudadela.test', 'Perfumería', 'Shampoo + acondicionador en promo', 'Segunda unidad al 50%.', 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=80'
    UNION ALL SELECT 'farmacia.bienestar@ciudadela.test', 'Salud', 'Vitaminas con 30% de descuento', 'Hasta agotar stock de laboratorio adherido.', 'https://images.unsplash.com/photo-1585435557343-3b092031a831?auto=format&fit=crop&w=900&q=80'
    UNION ALL SELECT 'ferreteria.hogar@ciudadela.test', 'Ferretería', 'Kit de herramientas con 15% OFF', 'Incluye martillo, pinza y destornilladores.', 'https://images.unsplash.com/photo-1572981779307-38b8cabb2407?auto=format&fit=crop&w=900&q=80'
    UNION ALL SELECT 'deportes.activos@ciudadela.test', 'Deportes', 'Zapatillas running con envío local', 'Talles seleccionados en oferta por tiempo limitado.', 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=80'
    UNION ALL SELECT 'deportes.activos@ciudadela.test', 'Deportes', 'Remeras técnicas 3x2', 'Combinables entre talles y colores.', 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=900&q=80'
    UNION ALL SELECT 'deportes.activos@ciudadela.test', 'Deportes', 'Mochilas urbanas 20% OFF', 'Promo por compra presencial.', 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=80'
    UNION ALL SELECT 'kiosco.24hs@ciudadela.test', 'Kiosco', 'Snacks + bebida combo ahorro', 'Ideal para la tarde, precio promocional.', 'https://images.unsplash.com/photo-1577801592644-6af7635a60f9?auto=format&fit=crop&w=900&q=80'
    UNION ALL SELECT 'kiosco.24hs@ciudadela.test', 'Kiosco', 'Golosinas surtidas 2x1', 'Promo válida de lunes a viernes.', 'https://images.unsplash.com/photo-1582058091505-f87a2e55a40f?auto=format&fit=crop&w=900&q=80'
) AS data ON data.email = u.email
WHERE u.role = 'business'
  AND NOT EXISTS (
      SELECT 1
      FROM offers o
      WHERE o.user_id = u.id
        AND o.title = data.title
  );
