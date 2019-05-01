<?php
namespace  SaliBhdr\TyphoonRate\ServiceProviders;

use SaliBhdr\TyphoonRate\Commands\MigrationCommand;
use Illuminate\Support\ServiceProvider;

class TyphoonRateableServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->commands('command.tyRateable.migration');
        $this->app->bind('command.tyRateable.migration', function ($app) {
            return new MigrationCommand();
        }, TRUE);
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.tyRateable.migration',
        ];
    }
}
