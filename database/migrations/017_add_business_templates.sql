-- Migration: Add Business Templates support
-- Creates tables and columns for business template system

-- Table: business_templates
CREATE TABLE IF NOT EXISTS business_templates (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    key TEXT UNIQUE NOT NULL COLLATE NOCASE,
    name TEXT NOT NULL,
    icon TEXT,
    description TEXT,
    fields_json TEXT,
    is_active INTEGER DEFAULT 1,
    display_order INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add columns to users table for template selection
ALTER TABLE users ADD COLUMN template_key TEXT DEFAULT 'default';
ALTER TABLE users ADD COLUMN template_data_json TEXT;

-- Create index for template lookups
CREATE INDEX idx_business_templates_key ON business_templates(key);
CREATE INDEX idx_business_templates_active ON business_templates(is_active);
CREATE INDEX idx_users_template ON users(template_key);

-- Insert predefined templates
INSERT INTO business_templates (key, name, icon, description, fields_json, display_order)
VALUES
    ('default', 'General', 'briefcase', 'Plantilla general sin restricciones', '[]', 0),
    ('restaurant', 'Restaurante/Bar', 'utensils', 'Para restaurantes, bares y comedores', '{"additional_fields":["horarios","tipo_comida","ambiente"]}', 1),
    ('pharmacy', 'Farmacia/Salud', 'pill', 'Para farmacias, consultorios y servicios de salud', '{"additional_fields":["medicamentos","servicios_especiales"]}', 2),
    ('retail', 'Tienda/Comercio', 'shopping-cart', 'Para tiendas de ropa, electrónica y retail', '{"additional_fields":["productos","categorias","marcas"]}', 3),
    ('services', 'Servicios', 'wrench', 'Para servicios profesionales y técnicos', '{"additional_fields":["horarios","especialidades","areas"]}', 4),
    ('barber_beauty', 'Barbería/Belleza', 'scissors', 'Para barberías, peluquerías y salones de belleza', '{"additional_fields":["servicios","precios","horarios"]}', 5),
    ('technology', 'Tecnología', 'cpu', 'Para tiendas de electrónica y servicios tech', '{"additional_fields":["especificaciones","garantia","modelos"]}', 6),
    ('real_estate', 'Inmuebles', 'home', 'Para inmobiliarias y propiedades', '{"additional_fields":["caracteristicas","precio","ubicacion"]}', 7);
