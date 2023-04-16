<?php

declare(strict_types = 1);

namespace Yesccx\DBQuery;

use Illuminate\Support\ServiceProvider;

class DBQueryProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->configure();

        $this->registerDependencies();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPublishing();
    }

    /**
     * Register dependencies
     *
     * @return void
     */
    protected function registerDependencies(): void
    {
        foreach (config('db-query.dependencies', []) as $contract => $target) {
            $this->app->bind($contract, $target);
        }
    }

    /**
     * Setup the configuration.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/db-query.php',
            'db-query'
        );
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/db-query.php' => config_path('db-query.php'),
        ], 'db-query-config');
    }
}
