<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\PageAction;
use App\Domain\Site\SettingsRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UpdateSettingsAction extends PageAction
{
    private const EDITABLE_KEYS = [
        'site_name',
        'hero_badge',
        'hero_title',
        'hero_description',
        'hero_primary_cta',
        'merchant_badge',
        'merchant_title',
        'merchant_description',
        'footer_tagline',
        'default_user_publish_mode',
    ];

    private const ALLOWED_USER_PUBLISH_MODES = ['direct', 'review', 'profile_required'];

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly SettingsRepository $settingsRepository
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $data = (array) $request->getParsedBody();
        $payload = [];

        foreach (self::EDITABLE_KEYS as $key) {
            $payload[$key] = trim((string) ($data[$key] ?? ''));
        }

        if (!in_array($payload['default_user_publish_mode'], self::ALLOWED_USER_PUBLISH_MODES, true)) {
            $payload['default_user_publish_mode'] = 'review';
        }

        $this->settingsRepository->updateMany($payload);
        $this->flash('success', 'Los textos y reglas de publicación fueron actualizados.');

        return $this->redirect($response, '/admin');
    }
}
