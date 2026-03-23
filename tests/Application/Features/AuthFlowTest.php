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

    public function testBusinessRegisterRejectsInvalidSocialUrl(): void
    {
        $app = $this->getAppInstance();
        $request = $this->createRequest('POST', '/register')->withParsedBody([
            'role' => 'business',
            'email' => 'nuevo-negocio@ofertascerca.test',
            'password' => 'supersecreto',
            'password_confirmation' => 'supersecreto',
            'business_name' => 'Nuevo Negocio',
            'whatsapp' => '+54 9 11 4444 5555',
            'street' => 'Calle Falsa',
            'street_number' => '123',
            'postal_code' => '1000',
            'city' => 'Buenos Aires',
            'municipality' => 'Comuna 1',
            'province' => 'Buenos Aires',
            'address_lat' => '-34.6037',
            'address_lon' => '-58.3816',
            'instagram_url' => 'notaurl',
        ]);
        $response = $app->handle($request);

        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/register', $response->getHeaderLine('Location'));
        self::assertSame('La URL de Instagram no es válida.', $_SESSION['flash']['form_errors']['instagram_url'] ?? null);
    }
}
