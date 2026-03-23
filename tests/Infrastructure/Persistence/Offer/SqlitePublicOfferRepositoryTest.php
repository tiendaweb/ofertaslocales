<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\Offer;

use App\Infrastructure\Persistence\Database\SqliteBootstrapper;
use App\Infrastructure\Persistence\Offer\SqlitePublicOfferRepository;
use PDO;
use Tests\TestCase;

class SqlitePublicOfferRepositoryTest extends TestCase
{
    public function testFindActiveOffersReturnsOnlyCurrentlyVisibleOffers(): void
    {
        $databasePath = tempnam(sys_get_temp_dir(), 'ofertas-public-offers-');
        $pdo = new PDO('sqlite:' . $databasePath);
        $bootstrapper = new SqliteBootstrapper(
            __DIR__ . '/../../../../database/migrations'
        );
        $bootstrapper->bootstrap($pdo);

        $pdo->exec(
            "INSERT INTO offers (
                user_id,
                category,
                title,
                description,
                whatsapp,
                location,
                status,
                created_at,
                expires_at
            ) VALUES (
                2,
                'Hogar',
                'Oferta expirada',
                'Ya no debería verse',
                '+54 9 11 1234 5678',
                'Barrio Norte',
                'active',
                datetime('now', '-2 day'),
                datetime('now', '-1 day')
            )"
        );
        $pdo->exec(
            "INSERT INTO offers (
                user_id,
                category,
                title,
                description,
                whatsapp,
                location,
                status,
                created_at,
                expires_at
            ) VALUES (
                2,
                'Hogar',
                'Oferta pendiente',
                'Tampoco debería verse',
                '+54 9 11 1234 5678',
                'Barrio Norte',
                'pending',
                datetime('now'),
                datetime('now', '+1 day')
            )"
        );

        $repository = new SqlitePublicOfferRepository($pdo);
        $offers = $repository->findActiveOffers();

        $this->assertCount(2, $offers);
        $this->assertSame(2, $repository->countActiveOffers());
        $this->assertSame(
            [
                'Zapatillas urbanas con envío local',
                'Combo desayuno con 25% de descuento',
            ],
            array_column($offers, 'title')
        );

        @unlink($databasePath);
    }
}
