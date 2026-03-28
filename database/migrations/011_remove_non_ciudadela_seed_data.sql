PRAGMA foreign_keys = ON;

DELETE FROM offers
WHERE user_id IN (
    SELECT id
    FROM users
    WHERE email IN ('panaderia@barrio.test', 'deportes@centro.test')
)
OR title IN (
    'Combo desayuno con 25% de descuento',
    'Zapatillas urbanas con envío local',
    'Docena de facturas recién horneadas'
);

DELETE FROM users
WHERE email IN ('panaderia@barrio.test', 'deportes@centro.test');
