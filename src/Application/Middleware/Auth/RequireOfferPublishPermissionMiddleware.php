<?php

declare(strict_types=1);

namespace App\Application\Middleware\Auth;

use App\Application\Auth\AuthService;
use App\Application\Service\OfferPublishPolicy;
use App\Domain\Site\SettingsRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class RequireOfferPublishPermissionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly SettingsRepository $settingsRepository,
        private readonly OfferPublishPolicy $offerPublishPolicy
    ) {
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $user = $this->authService->currentUser();
        if ($user === null) {
            $_SESSION['flash']['error'] = 'Debes iniciar sesión para publicar ofertas.';

            return (new SlimResponse())->withHeader('Location', '/login')->withStatus(302);
        }

        $settings = $this->settingsRepository->findByKeys(['approval_mode']);
        $policy = $this->offerPublishPolicy->resolve($user, $settings);

        if (($policy['can_publish'] ?? false) === true) {
            return $handler->handle($request);
        }

        $_SESSION['flash']['error'] = (string) ($policy['blocked_reason'] ?? 'No tienes permisos para publicar ofertas.');

        return (new SlimResponse())->withHeader('Location', '/panel')->withStatus(302);
    }
}
