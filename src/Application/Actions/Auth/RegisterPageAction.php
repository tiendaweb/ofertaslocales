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
        return $this->renderPage($response, 'pages/auth/register.php', [
            'pageTitle' => 'Crear cuenta | OfertasCerca',
            'currentRoute' => 'registro',
        ]);
    }
}
