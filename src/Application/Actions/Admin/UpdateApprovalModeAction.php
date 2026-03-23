<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\PageAction;
use App\Domain\Site\SettingsRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UpdateApprovalModeAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly SettingsRepository $settingsRepository
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $mode = (string) (((array) $request->getParsedBody())['approval_mode'] ?? 'manual');
        if (!in_array($mode, ['auto', 'manual'], true)) {
            $this->flash('error', 'Selecciona un modo de aprobación válido.');

            return $this->redirect($response, '/admin');
        }

        $this->settingsRepository->updateMany(['approval_mode' => $mode]);
        $this->flash('success', 'El modo de aprobación fue actualizado.');

        return $this->redirect($response, '/admin');
    }
}
