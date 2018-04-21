<?php namespace Yorki\Repositories;

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

        $this->commands('command.repository.make');
        $this->commands('command.repository-model.make');
    }
}