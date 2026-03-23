<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\Business;

use App\Infrastructure\Persistence\Business\SqliteBusinessDirectoryRepository;
use App\Infrastructure\Persistence\Database\SqliteBootstrapper;
use PDO;
use Tests\TestCase;

class SqliteBusinessDirectoryRepositoryTest extends TestCase
{
    public function testFindBusinessesWithActiveOffersExcludesBusinessesWithoutVisibleOffers(): void
    {
        $databasePath = tempnam(sys_get_temp_dir(), 'ofertas-businesses-');
        $pdo = new PDO('sqlite:' . $databasePath);
        $bootstrapper = new SqliteBootstrapper(
            __DIR__ . '/../../../../database/migrations'
        );
        $bootstrapper->bootstrap($pdo);

        $pdo->exec(
            "INSERT INTO users (
                email,
                password,
                role,
                business_name,
                whatsapp,
                created_at
            ) VALUES (
                'libreria@barrio.test',
                'secret',
                'business',
                'Librería Barrio',
                '+54 9 11 3333 4444',
                datetime('now')
            )"
        );
        $businessId = (int) $pdo->lastInsertId();
        $pdo->exec(
            sprintf(
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
                    %d,
                    'Libros',
                    'Oferta expirada',
                    'No debe aparecer',
                    '+54 9 11 3333 4444',
                    'Centro',
                    'active',
                    datetime('now', '-3 day'),
                    datetime('now', '-2 day')
                )",
                $businessId
            )
        );

        $repository = new SqliteBusinessDirectoryRepository($pdo);
        $businesses = $repository->findBusinessesWithActiveOffers();

        $this->assertCount(2, $businesses);
        $this->assertSame(2, $repository->countBusinessesWithActiveOffers());
        $this->assertSame(
            ['Deportes Centro', 'Panadería del Barrio'],
            array_column($businesses, 'business_name')
        );

        @unlink($databasePath);
    }
}
