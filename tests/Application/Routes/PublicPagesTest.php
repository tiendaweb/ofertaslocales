<?php

declare(strict_types=1);

namespace Tests\Application\Routes;

use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    /**
     * @dataProvider routeProvider
     */
    public function testBaseRoutesRespondSuccessfully(string $path, array $session = []): void
    {
        $_SESSION = $session;

        $app = $this->getAppInstance();
        $request = $this->createRequest('GET', $path, ['HTTP_ACCEPT' => 'text/html']);
        $response = $app->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('text/html', $response->getHeaderLine('Content-Type'));
    }

    public function routeProvider(): array
    {
        return [
            ['/'],
            ['/ofertas'],
            ['/negocios'],
            ['/negocios/2'],
            ['/mapa'],
            ['/login'],
            ['/register'],
            ['/panel', [
                'auth' => [
                    'user' => [
                        'id' => 2,
                        'email' => 'panaderia@barrio.test',
                        'role' => 'business',
                        'business_name' => 'Panadería del Barrio',
                        'whatsapp' => '+54 9 11 1234 5678',
                    ],
                ],
            ]],
            ['/admin', [
                'auth' => [
                    'user' => [
                        'id' => 1,
                        'email' => 'admin@ofertascerca.test',
                        'role' => 'admin',
                        'business_name' => 'OfertasCerca Admin',
                        'whatsapp' => '+54 9 11 0000 0000',
                    ],
                ],
            ]],
        ];
    }
}
