ALTER TABLE users ADD COLUMN bio TEXT;
ALTER TABLE users ADD COLUMN instagram_url TEXT;
ALTER TABLE users ADD COLUMN facebook_url TEXT;
ALTER TABLE users ADD COLUMN tiktok_url TEXT;
ALTER TABLE users ADD COLUMN website_url TEXT;
ALTER TABLE users ADD COLUMN logo_url TEXT;

UPDATE users
SET bio = 'Panadería artesanal con promos diarias para el barrio.',
    instagram_url = 'https://instagram.com/panaderiadelbarrio',
    facebook_url = 'https://facebook.com/panaderiadelbarrio',
    website_url = 'https://panaderiadelbarrio.test',
    logo_url = 'https://images.unsplash.com/photo-1612198790700-0ff08cb726e5?auto=format&fit=crop&w=240&q=80'
WHERE id = 2;

UPDATE users
SET bio = 'Tienda deportiva con envíos en el día y atención por WhatsApp.',
    instagram_url = 'https://instagram.com/deportescentro',
    tiktok_url = 'https://tiktok.com/@deportescentro',
    website_url = 'https://deportescentro.test',
    logo_url = 'https://images.unsplash.com/photo-1518459031867-a89b944bffe4?auto=format&fit=crop&w=240&q=80'
WHERE id = 3;
