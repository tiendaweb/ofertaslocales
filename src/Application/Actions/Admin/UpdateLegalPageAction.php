<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\Action;
use App\Application\Service\LegalPageService;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateLegalPageAction extends Action
{
    private LegalPageService $legalPageService;

    public function __construct(LegalPageService $legalPageService)
    {
        $this->legalPageService = $legalPageService;
    }

    protected function action(): Response
    {
        $request = $this->getRequest();
        $data = $request->getParsedBody();

        $pageKey = $data['page_key'] ?? null;
        $title = $data['title'] ?? null;
        $content = $data['content'] ?? null;

        // Validate required fields
        if (!$pageKey || !$title || !$content) {
            return $this->respondWithJson(['error' => 'Missing required fields'], 400);
        }

        // Validate page key
        $validPages = ['terms', 'privacy', 'cookies'];
        if (!in_array($pageKey, $validPages, true)) {
            return $this->respondWithJson(['error' => 'Invalid page key'], 400);
        }

        // Get current user ID
        $currentUser = $request->getAttribute('currentUser') ?? [];
        $userId = $currentUser['id'] ?? null;

        // Update legal page
        $updated = $this->legalPageService->updatePage($pageKey, $title, $content, $userId);

        if (!$updated) {
            return $this->respondWithJson(['error' => 'Failed to update legal page'], 500);
        }

        return $this->respondWithJson([
            'success' => true,
            'message' => 'Página actualizada correctamente',
            'data' => $updated,
        ]);
    }
}
