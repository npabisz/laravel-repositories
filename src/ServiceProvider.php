<?php namespace Yorkii\Repositories;

use Yorkii\Repositories\Console\Commands\MakeRepository;

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

        $this->commands('command.repository.make');
    }
}