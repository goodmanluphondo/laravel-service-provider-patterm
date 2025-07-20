<?php

namespace GoodmanLuphondo\LaravelServiceRepositoryPattern\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use GoodmanLuphondo\LaravelServiceRepositoryPattern\Providers\ServiceRepositoryPatternServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ServiceRepositoryPatternServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // optional: configure test database, paths, etc.
    }
}
