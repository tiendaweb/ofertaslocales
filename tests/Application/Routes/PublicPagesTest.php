<?php

declare(strict_types=1);

namespace Tests\Application\Routes;

use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    /**
     * @dataProvider routeProvider
     */
    public function testBaseRoutesRespondSuccessfully(string $path): void
    {
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
            ['/mapa'],
            ['/login'],
            ['/register'],
            ['/panel'],
            ['/admin'],
        ];
    }
}
