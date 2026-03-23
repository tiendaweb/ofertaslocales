<?php

declare(strict_types=1);

use App\Application\Actions\Admin\AdminDashboardAction;
use App\Application\Actions\Admin\PanelAction;
use App\Application\Actions\Auth\LoginPageAction;
use App\Application\Actions\Auth\RegisterPageAction;
use App\Application\Actions\Public\BusinessesAction;
use App\Application\Actions\Public\HomeAction;
use App\Application\Actions\Public\MapAction;
use App\Application\Actions\Public\OffersAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
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
    $app->get('/register', RegisterPageAction::class)->setName('registro');
    $app->get('/panel', PanelAction::class)->setName('panel');
    $app->get('/admin', AdminDashboardAction::class)->setName('admin');

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
