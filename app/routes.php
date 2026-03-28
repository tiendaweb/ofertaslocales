<?php

declare(strict_types=1);

use App\Application\Actions\Admin\AdminDashboardAction;
use App\Application\Actions\Admin\CreateAdminUserAction;
use App\Application\Actions\Admin\DeleteAdminOfferAction;
use App\Application\Actions\Admin\CreateAdminCategoryAction;
use App\Application\Actions\Admin\DeleteAdminCategoryAction;
use App\Application\Actions\Admin\ImpersonateAdminUserAction;
use App\Application\Actions\Admin\ListAdminUsersAction;
use App\Application\Actions\Admin\SuspendAdminUserAction;
use App\Application\Actions\Admin\UnsuspendAdminUserAction;
use App\Application\Actions\Admin\UpdateAdminUserAction;
use App\Application\Actions\Admin\UpdateAdminCategoryStatusAction;
use App\Application\Actions\Admin\UpdateAdminCategoryAction;
use App\Application\Actions\Admin\UpdateApprovalModeAction;
use App\Application\Actions\Admin\UpdateInlineContentAction;
use App\Application\Actions\Admin\UpdateOfferStatusAction;
use App\Application\Actions\Admin\UpdateAdminOfferAction;
use App\Application\Actions\Admin\UpdateSeoAction;
use App\Application\Actions\Admin\UpdateSettingsAction;
use App\Application\Actions\Auth\LoginPageAction;
use App\Application\Actions\Auth\LoginSubmitAction;
use App\Application\Actions\Auth\LogoutAction;
use App\Application\Actions\Auth\RegisterPageAction;
use App\Application\Actions\Auth\RegisterSubmitAction;
use App\Application\Actions\Auth\StopImpersonationAction;
use App\Application\Actions\Business\BusinessDashboardAction;
use App\Application\Actions\Business\CreateOfferAction;
use App\Application\Actions\Business\DeleteBusinessOfferAction;
use App\Application\Actions\Business\EditBusinessProfilePageAction;
use App\Application\Actions\Business\UserProfilePageAction;
use App\Application\Actions\Business\UpdateUserProfileAction;
use App\Application\Actions\Business\UpdateBusinessOfferAction;
use App\Application\Actions\Business\UpdateBusinessProfileAction;
use App\Application\Actions\Public\BusinessesAction;
use App\Application\Actions\Public\BusinessDetailAction;
use App\Application\Actions\Public\HomeAction;
use App\Application\Actions\Public\MapAction;
use App\Application\Actions\Public\OffersAction;
use App\Application\Actions\Public\SubmitPublicOfferAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use App\Application\Middleware\Auth\RequireAdminMiddleware;
use App\Application\Middleware\Auth\RequireAuthenticationMiddleware;
use App\Application\Middleware\Auth\RequireBusinessMiddleware;
use App\Application\Middleware\Auth\RequireOfferPublishPermissionMiddleware;
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
    $app->get('/negocios/{id:[0-9]+}', BusinessDetailAction::class)->setName('negocios.detalle');
    $app->get('/mapa', MapAction::class)->setName('mapa');
    $app->post('/publicar', SubmitPublicOfferAction::class)->setName('publicar');

    $app->get('/login', LoginPageAction::class)->setName('login');
    $app->post('/login', LoginSubmitAction::class);
    $app->get('/register', RegisterPageAction::class)->setName('registro');
    $app->post('/register', RegisterSubmitAction::class);
    $app->post('/logout', LogoutAction::class)->setName('logout');
    $app->post('/impersonation/stop', StopImpersonationAction::class)
        ->add(RequireAuthenticationMiddleware::class)
        ->setName('impersonation.stop');

    $app->group('/panel', function (Group $group) {
        $group->get('', BusinessDashboardAction::class)->setName('panel');
        $group->post('/ofertas', CreateOfferAction::class)
            ->add(RequireOfferPublishPermissionMiddleware::class)
            ->setName('panel.ofertas.crear');
        $group->post('/ofertas/{id}', UpdateBusinessOfferAction::class)->setName('panel.ofertas.actualizar');
        $group->post('/ofertas/{id}/eliminar', DeleteBusinessOfferAction::class)->setName('panel.ofertas.eliminar');
        $group->get('/negocio/editar', EditBusinessProfilePageAction::class)->setName('panel.negocio.editar');
        $group->post('/negocio/editar', UpdateBusinessProfileAction::class)->setName('panel.negocio.actualizar');
        $group->get('/perfil', UserProfilePageAction::class)->setName('panel.perfil');
        $group->post('/perfil', UpdateUserProfileAction::class)->setName('panel.perfil.actualizar');
    })->add(RequireBusinessMiddleware::class)->add(RequireAuthenticationMiddleware::class);

    $app->group('/admin', function (Group $group) {
        $group->get('', AdminDashboardAction::class)->setName('admin');
        $group->post('/offers/{id}/status', UpdateOfferStatusAction::class)->setName('admin.offers.status');
        $group->post('/offers/{id}/update', UpdateAdminOfferAction::class)->setName('admin.offers.update');
        $group->post('/offers/{id}/delete', DeleteAdminOfferAction::class)->setName('admin.offers.delete');
        $group->post('/approval-mode', UpdateApprovalModeAction::class)->setName('admin.approval-mode');
        $group->post('/categories', CreateAdminCategoryAction::class)->setName('admin.categories.create');
        $group->post('/categories/{id:[0-9]+}/status', UpdateAdminCategoryStatusAction::class)->setName('admin.categories.status');
        $group->post('/categories/{id:[0-9]+}/update', UpdateAdminCategoryAction::class)->setName('admin.categories.update');
        $group->post('/categories/{id:[0-9]+}/delete', DeleteAdminCategoryAction::class)->setName('admin.categories.delete');
        $group->post('/settings', UpdateSettingsAction::class)->setName('admin.settings');
        $group->post('/seo/{page_name}', UpdateSeoAction::class)->setName('admin.seo');
        $group->post('/inline-content', UpdateInlineContentAction::class)->setName('admin.inline-content');
        $group->group('/users', function (Group $usersGroup) {
            $usersGroup->get('', ListAdminUsersAction::class)->setName('admin.users.index');
            $usersGroup->post('', CreateAdminUserAction::class)->setName('admin.users.create');
            $usersGroup->post('/{id}', UpdateAdminUserAction::class)->setName('admin.users.update');
            $usersGroup->post('/{id}/suspend', SuspendAdminUserAction::class)->setName('admin.users.suspend');
            $usersGroup->post('/{id}/unsuspend', UnsuspendAdminUserAction::class)->setName('admin.users.unsuspend');
            $usersGroup->post('/{id}/impersonate', ImpersonateAdminUserAction::class)->setName('admin.users.impersonate');
        })->add(RequireAdminMiddleware::class);
    })->add(RequireAdminMiddleware::class)->add(RequireAuthenticationMiddleware::class);

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
