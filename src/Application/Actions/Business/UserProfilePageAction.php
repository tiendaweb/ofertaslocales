<?php

declare(strict_types=1);

namespace App\Application\Actions\Business;

use App\Application\Actions\PageAction;
use App\Domain\Site\SettingsRepository;
use App\Domain\User\AccountRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserProfilePageAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly AccountRepository $accountRepository,
        private readonly SettingsRepository $settingsRepository
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $user = $this->currentUser();
        if (!is_array($user) || !isset($user['id'])) {
            $this->flash('error', 'Debes iniciar sesión para ver tu perfil.');

            return $this->redirect($response, '/login');
        }

        $account = $this->accountRepository->findById((int) $user['id']);
        if ($account === null) {
            $this->flash('error', 'No se encontró tu cuenta.');

            return $this->redirect($response, '/panel');
        }

        $settings = $this->settingsRepository->findByKeys(['location_catalog_json']);

        return $this->renderPage($response, 'pages/admin/user-profile.php', [
            'pageTitle' => 'Mi perfil | OfertasLocales',
            'currentRoute' => 'panel.perfil',
            'account' => $account,
            'locationCatalog' => $this->decodeLocationCatalog((string) ($settings['location_catalog_json'] ?? '')),
        ]);
    }

    private function decodeLocationCatalog(string $json): array
    {
        $decoded = json_decode($json, true);
        if (!is_array($decoded) || !isset($decoded['provinces'], $decoded['municipalities'])) {
            return [
                'provinces' => ['Buenos Aires'],
                'municipalities' => [
                    'Tres de Febrero' => ['Ciudadela', 'Caseros', 'Santos Lugares', 'Villa Bosch', 'Martín Coronado'],
                ],
            ];
        }

        return $decoded;
    }
}
