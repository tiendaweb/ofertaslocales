ALTER TABLE users ADD COLUMN cover_url TEXT;

UPDATE users
SET cover_url = 'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?auto=format&fit=crop&w=1200&q=80'
WHERE id = 2 AND (cover_url IS NULL OR trim(cover_url) = '');

UPDATE users
SET cover_url = 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=1200&q=80'
WHERE id = 3 AND (cover_url IS NULL OR trim(cover_url) = '');
