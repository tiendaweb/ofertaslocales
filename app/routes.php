<?php

declare(strict_types=1);

use App\Application\Actions\Admin\AdminDashboardAction;
use App\Application\Actions\Admin\UpdateApprovalModeAction;
use App\Application\Actions\Admin\UpdateOfferStatusAction;
use App\Application\Actions\Admin\UpdateSeoAction;
use App\Application\Actions\Admin\UpdateSettingsAction;
use App\Application\Actions\Auth\LoginPageAction;
use App\Application\Actions\Auth\LoginSubmitAction;
use App\Application\Actions\Auth\LogoutAction;
use App\Application\Actions\Auth\RegisterPageAction;
use App\Application\Actions\Auth\RegisterSubmitAction;
use App\Application\Actions\Business\BusinessDashboardAction;
use App\Application\Actions\Business\CreateOfferAction;
use App\Application\Actions\Public\BusinessesAction;
use App\Application\Actions\Public\HomeAction;
use App\Application\Actions\Public\MapAction;
use App\Application\Actions\Public\OffersAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use App\Application\Middleware\Auth\RequireAdminMiddleware;
use App\Application\Middleware\Auth\RequireAuthenticationMiddleware;
use App\Application\Middleware\Auth\RequireBusinessMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response;
    });

    $app->get('/', HomeAction::class)->setName('inicio');
    $app->get('/ofertas', OffersAction::class)->setName('ofertas');
    $app->get('/negocios', BusinessesAction::class)->setName('negocios');
    $app->get('/mapa', MapAction::class)->setName('mapa');

    $app->get('/login', LoginPageAction::class)->setName('login');
    $app->post('/login', LoginSubmitAction::class);
    $app->get('/register', RegisterPageAction::class)->setName('registro');
    $app->post('/register', RegisterSubmitAction::class);
    $app->post('/logout', LogoutAction::class)->setName('logout');

    $app->group('/panel', function (Group $group) {
        $group->get('', BusinessDashboardAction::class)->setName('panel');
        $group->post('/ofertas', CreateOfferAction::class)->setName('panel.ofertas.crear');
    })->add(RequireBusinessMiddleware::class)->add(RequireAuthenticationMiddleware::class);

    $app->group('/admin', function (Group $group) {
        $group->get('', AdminDashboardAction::class)->setName('admin');
        $group->post('/offers/{id}/status', UpdateOfferStatusAction::class)->setName('admin.offers.status');
        $group->post('/approval-mode', UpdateApprovalModeAction::class)->setName('admin.approval-mode');
        $group->post('/settings', UpdateSettingsAction::class)->setName('admin.settings');
        $group->post('/seo/{page_name}', UpdateSeoAction::class)->setName('admin.seo');
    })->add(RequireAdminMiddleware::class)->add(RequireAuthenticationMiddleware::class);

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
