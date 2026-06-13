<?php

namespace Vlancy\LaravelApiResponse\Providers;

use Illuminate\Support\ServiceProvider;
use Vlancy\LaravelApiResponse\Console\PublishErrorCodesEnumCommand;
use Vlancy\LaravelApiResponse\Services\APIResponseService;

class APIResponseProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/api-response.php', 'api-response');

        $this->app->bind('lapi-response', function () {
            return new APIResponseService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/api-response.php' => config_path('api-response.php'),
            ], 'lapi-response-config');

            $this->commands([
                PublishErrorCodesEnumCommand::class,
            ]);
        }

        if (class_exists('\Illuminate\Foundation\Exceptions\Handler')) {
            $this->app->singleton(
                \Illuminate\Contracts\Debug\ExceptionHandler::class,
                \Vlancy\LaravelApiResponse\Exceptions\Handler::class
            );
        }
        if (class_exists('\Laravel\Lumen\Exceptions\Handler')) {
            $this->app->singleton(
                \Illuminate\Contracts\Debug\ExceptionHandler::class,
                \Vlancy\LaravelApiResponse\Exceptions\LumenHandler::class
            );
        }
    }
}
