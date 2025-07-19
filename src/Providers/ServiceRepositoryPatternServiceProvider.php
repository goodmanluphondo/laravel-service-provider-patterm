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
            __DIR__.'/../stubs' => $this->app->basePath('stubs'),
            __DIR__.'/../Console/Commands/MakeServiceCommand.php' => $this->app->basePath('app/Console/Commands/MakeServiceCommand.php'),
            __DIR__.'/../Interfaces/BaseInterface.php' => $this->app->basePath('app/Interfaces/BaseInterface.php'),
            __DIR__.'/../Repositories/Repository.php' => $this->app->basePath('app/Repositories/Repository.php'),
            __DIR__.'/ServiceRepositoryPatternServiceProvider.php' => $this->app->basePath('app/Providers/ServiceRepositoryPatternServiceProvider.php'),
        ], 'service-repository');

        if ($this->app->runningInConsole()) {
            $this->commands([MakeServiceCommand::class]);
        }
    }
}