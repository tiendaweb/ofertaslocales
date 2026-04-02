UPDATE users
SET whatsapp = REPLACE(
    REPLACE(
        REPLACE(
            REPLACE(
                REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(COALESCE(whatsapp, ''), '+', ''),
                        ' ', ''),
                    '-', ''),
                '(', ''),
            ')', ''),
        '.', ''),
    '/', ''),
CHAR(9), '')
WHERE whatsapp IS NOT NULL;

UPDATE offers
SET whatsapp = REPLACE(
    REPLACE(
        REPLACE(
            REPLACE(
                REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(COALESCE(whatsapp, ''), '+', ''),
                        ' ', ''),
                    '-', ''),
                '(', ''),
            ')', ''),
        '.', ''),
    '/', ''),
CHAR(9), '')
WHERE whatsapp IS NOT NULL;
