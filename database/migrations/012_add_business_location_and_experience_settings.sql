ALTER TABLE users ADD COLUMN between_streets TEXT;

INSERT OR IGNORE INTO settings (key, value) VALUES
    ('contact_whatsapp', '+54 9 11 0000 0000'),
    ('maintenance_mode', '0'),
    ('maintenance_message', 'Estamos realizando tareas de mantenimiento. Volvé a intentar en unos minutos.'),
    ('frontend_custom_css', ''),
    ('frontend_custom_js', ''),
    ('admin_custom_css', ''),
    ('admin_custom_js', '');
