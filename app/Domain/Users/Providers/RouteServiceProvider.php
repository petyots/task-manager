<?php

namespace App\Domain\Users\Providers;

use App\Domain\Users\Http\Controllers\Auth\AuthenticationController;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;

class RouteServiceProvider extends ServiceProvider
{
    public function map(Router $router)
    {
        $this->mapAuthRoutes($router);
    }
    private function mapAuthRoutes(Router $router)
    {
        $router->group([
            'prefix' => 'auth',
            'as' => 'auth.',
            'middleware' => ['api']
        ], function (Router $router) {
            $this->mapAuthRoutesWhenGuest($router);
            $this->mapAuthRoutesWhenAuthenticationIsRequired($router);
        });
    }

    private function mapAuthRoutesWhenGuest(Router $router)
    {
        $router->post('register', [AuthenticationController::class, 'register'])->name('register');
        $router->post('login', [AuthenticationController::class, 'login'])->name('login');
    }

    private function mapAuthRoutesWhenAuthenticationIsRequired(Router $router)
    {
        $router->group([
            'middleware' => ['auth:api']
        ], function (Router $router) {
            $router->post('logout', [AuthenticationController::class, 'logout'])->name('logout');
            $router->post('refresh', [AuthenticationController::class, 'refreshAuthToken'])->name('refresh_token');
        });
    }
}
