<?php

namespace App\Domain\Tasks\Providers;

use App\Domain\Tasks\Models\Task;
use App\Domain\Tasks\Policies\TaskPolicy;
use App\Infrastructure\Abstracts\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    protected bool $hasMigrations = true;

    protected array $providers = [
        RouteServiceProvider::class,
    ];

    protected array $policies = [
        Task::class => TaskPolicy::class,
    ];
}
