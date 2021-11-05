<?php

namespace App\Domain\Tasks\Providers;

use App\Domain\Tasks\Http\Controllers\TasksController;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;

class RouteServiceProvider extends ServiceProvider
{
    public function map(Router $router)
    {
        $this->mapApiRoutes($router);
    }

    private function mapApiRoutes(Router $router)
    {
        $router->group([
            'prefix' => 'api/task',
            'as' => 'api.task.'
        ], function (Router $router) {
            $this->mapApiRoutesWhenAuthenticationIsRequired($router);
        });
    }

    private function mapApiRoutesWhenAuthenticationIsRequired(Router $router)
    {
        $router->group([
            'middleware' => ['auth:api']
        ], function (Router $router) {
            $router->get('/', [TasksController::class, 'index'])->name('index');
            $router->post('/', [TasksController::class, 'store'])->name('store');
            $router->post('change-status/{task}', [TasksController::class, 'changeStatus'])
                ->name('change_status');
        });
    }
}
