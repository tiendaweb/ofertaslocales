<?php

declare(strict_types=1);

namespace App\Application\Actions\Auth;

use App\Application\Actions\PageAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RegisterPageAction extends PageAction
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        if ($this->currentUser() !== null) {
            $role = (string) ($this->currentUser()['role'] ?? 'user');
            $redirectTo = $role === 'admin' ? '/admin' : ($role === 'business' ? '/panel' : '/');

            return $this->redirect($response, $redirectTo);
        }

        $query = $request->getQueryParams();
        $sessionDraft = is_array($_SESSION['offer_draft'] ?? null) ? $_SESSION['offer_draft'] : [];
        $prefillOld = [
            'role' => $sessionDraft !== [] ? 'business' : 'user',
            'business_name' => trim((string) ($query['business_name'] ?? ($sessionDraft['business_name'] ?? ''))),
            'category' => trim((string) ($query['category'] ?? ($sessionDraft['category'] ?? ''))),
            'title' => trim((string) ($query['title'] ?? ($sessionDraft['title'] ?? ''))),
            'location' => trim((string) ($query['location'] ?? ($sessionDraft['location'] ?? ''))),
            'whatsapp' => trim((string) ($query['whatsapp'] ?? ($sessionDraft['whatsapp'] ?? ''))),
            'description' => trim((string) ($query['description'] ?? ($sessionDraft['description'] ?? ''))),
            'image_url' => trim((string) ($sessionDraft['image_url'] ?? '')),
        ];

        if (($query['role'] ?? null) !== null && in_array((string) $query['role'], ['user', 'business'], true)) {
            $prefillOld['role'] = (string) $query['role'];
        }

        if ($prefillOld['business_name'] === '' && ($query['negocio'] ?? null) !== null) {
            $prefillOld['business_name'] = trim((string) $query['negocio']);
        }

        if ($prefillOld['category'] === '' && ($query['categoria'] ?? null) !== null) {
            $prefillOld['category'] = trim((string) $query['categoria']);
        }

        if ($prefillOld['title'] === '' && ($query['oferta'] ?? null) !== null) {
            $prefillOld['title'] = trim((string) $query['oferta']);
        }

        if ($prefillOld['location'] === '' && ($query['ubicacion'] ?? null) !== null) {
            $prefillOld['location'] = trim((string) $query['ubicacion']);
        }

        if ($prefillOld['description'] === '' && ($query['detalle'] ?? null) !== null) {
            $prefillOld['description'] = trim((string) $query['detalle']);
        }

        if (
            $prefillOld['role'] === 'user'
            && (
                $prefillOld['business_name'] !== ''
                || $prefillOld['category'] !== ''
                || $prefillOld['title'] !== ''
                || $prefillOld['location'] !== ''
            )
        ) {
            $prefillOld['role'] = 'business';
        }

        return $this->renderPage($response, 'pages/auth/register.php', [
            'pageTitle' => 'Crear cuenta | OfertasLocales',
            'currentRoute' => 'registro',
            'prefillOld' => $prefillOld,
        ]);
    }
}
