<?php

namespace GoodmanLuphondo\LaravelServiceRepositoryPattern\Providers;

use GoodmanLuphondo\LaravelServiceRepositoryPattern\Console\Commands\MakeServiceCommand;
use Illuminate\Support\ServiceProvider;

class ServiceRepositoryPatternServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../stubs' => $this->app->basePath('stubs'),
            __DIR__.'/../Interfaces/BaseInterface.php' => $this->app->basePath('app/Interfaces/BaseInterface.php'),
            __DIR__.'/../Repositories/Repository.php' => $this->app->basePath('app/Repositories/Repository.php'),
            __DIR__.'/RepositoryServiceProvider.php' => $this->app->basePath('app/Providers/RepositoryServiceProvider.php'),
        ], 'service-repository-pattern');

        if ($this->app->runningInConsole()) {
            $this->commands([MakeServiceCommand::class]);
        }
    }
}