<?php namespace Yorki\Repositories;

use Yorki\Repositories\Console\Commands\MakeApi;
use Yorki\Repositories\Console\Commands\MakeMigration;
use Yorki\Repositories\Console\Commands\MakeModel;
use Yorki\Repositories\Console\Commands\MakeRepository;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.repository.make', function ($app) {
            return new MakeRepository($app['files']);
        });

        $this->app->singleton('command.repository-model.make', function ($app) {
            return new MakeModel($app['files']);
        });

        $this->app->singleton('command.repository-migration.make', function ($app) {
            return new MakeMigration($app['files']);
        });

        $this->app->singleton('command.repository-api.make', function ($app) {
            return new MakeApi($app['files']);
        });

        $this->commands('command.repository.make');
        $this->commands('command.repository-model.make');
        $this->commands('command.repository-migration.make');
        $this->commands('command.repository-api.make');
    }
}