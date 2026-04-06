<?php

declare(strict_types=1);

namespace App\Application\Actions\Public;

use App\Application\Actions\PageAction;
use App\Application\Service\LegalPageService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ViewLegalPageAction extends PageAction
{
    private LegalPageService $legalPageService;

    public function __construct(LegalPageService $legalPageService)
    {
        $this->legalPageService = $legalPageService;
    }

    protected function action(): Response
    {
        $request = $this->getRequest();
        $pageKey = $request->getAttribute('pageKey') ?? 'terms';

        // Validate page key
        $validPages = ['terms', 'privacy', 'cookies'];
        if (!in_array($pageKey, $validPages, true)) {
            return $this->notFoundResponse();
        }

        $legalPage = $this->legalPageService->getPage($pageKey);

        if (!$legalPage) {
            return $this->notFoundResponse();
        }

        $pageLabels = [
            'terms' => 'Términos y Condiciones',
            'privacy' => 'Política de Privacidad',
            'cookies' => 'Política de Cookies',
        ];

        $contentTemplate = 'pages/legal/page.php';

        return $this->renderPage(
            $this->getResponse(),
            $contentTemplate,
            [
                'legalPage' => $legalPage,
                'pageTitle' => $pageLabels[$pageKey] ?? 'Legal',
                'metaDescription' => $pageLabels[$pageKey],
            ]
        );
    }

    private function notFoundResponse(): Response
    {
        $response = $this->getResponse();
        $response = $response->withStatus(404);

        return $this->renderPage($response, 'pages/404.php', ['pageTitle' => 'Página no encontrada']);
    }
}
