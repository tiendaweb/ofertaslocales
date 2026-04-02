<?php

declare(strict_types=1);

namespace Tests\Application\Features;

use PDO;
use Tests\TestCase;

class SettingsIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        @unlink(__DIR__ . '/../../../database/app.sqlite');
        $_SESSION = [];
    }

    public function testAdminSettingsPersistAndRenderInDashboard(): void
    {
        $this->setAdminSession();

        $app = $this->getAppInstance();
        $request = $this->createRequest('POST', '/admin/settings')->withParsedBody([
            'site_name' => 'Mi Plaza Local',
            'contact_whatsapp' => '5491111111111',
            'maintenance_mode' => '1',
            'custom_css_frontend' => '.banner-test { color: red; }',
            'safe_mode' => '0',
        ]);
        $response = $app->handle($request);

        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/admin?tab=ajustes', $response->getHeaderLine('Location'));

        $dashboardRequest = $this->createRequest('GET', '/admin');
        $dashboardResponse = $app->handle($dashboardRequest);
        $dashboardHtml = (string) $dashboardResponse->getBody();

        self::assertStringContainsString('Mi Plaza Local', $dashboardHtml);
        self::assertStringContainsString('5491111111111', $dashboardHtml);
        self::assertStringContainsString('Auditoría reciente de ajustes críticos', $dashboardHtml);

        $pdo = new PDO('sqlite:' . __DIR__ . '/../../../database/app.sqlite');
        $auditCount = (int) $pdo->query('SELECT COUNT(*) FROM settings_audit_log WHERE setting_key = "site_name"')->fetchColumn();
        self::assertGreaterThan(0, $auditCount);
    }

    public function testSafeModeBlocksCustomCssRender(): void
    {
        $this->setAdminSession();

        $app = $this->getAppInstance();
        $saveRequest = $this->createRequest('POST', '/admin/settings')->withParsedBody([
            'custom_css_frontend' => '.modo-prueba-seguro { color: #123456; }',
            'safe_mode' => '1',
        ]);
        $app->handle($saveRequest);

        $publicRequest = $this->createRequest('GET', '/ofertas');
        $publicResponse = $app->handle($publicRequest);
        $publicHtml = (string) $publicResponse->getBody();

        self::assertStringNotContainsString('.modo-prueba-seguro', $publicHtml);
    }

    private function setAdminSession(): void
    {
        $this->setSession([
            'auth' => [
                'user' => [
                    'id' => 1,
                    'email' => 'admin@admin.com',
                    'role' => 'admin',
                    'business_name' => 'Administrador',
                    'whatsapp' => '5491100000000',
                ],
            ],
        ]);
    }
}
