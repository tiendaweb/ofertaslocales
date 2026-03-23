<?php

declare(strict_types=1);

namespace Tests\Application\Features;

use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        @unlink(__DIR__ . '/../../../database/app.sqlite');
        $_SESSION = [];
    }

    public function testLoginPageLoads(): void
    {
        $app = $this->getAppInstance();
        $request = $this->createRequest('GET', '/login');
        $response = $app->handle($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('Ingresar a tu cuenta', (string) $response->getBody());
    }

    public function testBusinessPanelRequiresAuthentication(): void
    {
        $app = $this->getAppInstance();
        $request = $this->createRequest('GET', '/panel');
        $response = $app->handle($request);

        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/login', $response->getHeaderLine('Location'));
    }

    public function testAdminRouteRejectsBusinessSession(): void
    {
        $this->setSession([
            'auth' => [
                'user' => [
                    'id' => 2,
                    'email' => 'panaderia@barrio.test',
                    'role' => 'business',
                    'business_name' => 'Panadería del Barrio',
                    'whatsapp' => '+54 9 11 1234 5678',
                ],
            ],
        ]);

        $app = $this->getAppInstance();
        $request = $this->createRequest('GET', '/admin');
        $response = $app->handle($request);

        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/login', $response->getHeaderLine('Location'));
    }

    public function testBusinessCanCreateOffer(): void
    {
        $this->setSession([
            'auth' => [
                'user' => [
                    'id' => 2,
                    'email' => 'panaderia@barrio.test',
                    'role' => 'business',
                    'business_name' => 'Panadería del Barrio',
                    'whatsapp' => '+54 9 11 1234 5678',
                ],
            ],
        ]);

        $app = $this->getAppInstance();
        $request = $this->createRequest('POST', '/panel/ofertas')->withParsedBody([
            'category' => 'Panadería',
            'title' => 'Promo test',
            'description' => 'Oferta de prueba para PHPUnit',
            'whatsapp' => '+54 9 11 1234 5678',
            'location' => 'Test 123',
            'lat' => '-34.60',
            'lon' => '-58.38',
            'expires_at' => gmdate('Y-m-d\TH:i', strtotime('+2 hours')),
        ]);
        $response = $app->handle($request);

        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/panel', $response->getHeaderLine('Location'));
    }
}
