<?php

namespace Vlancy\LaravelApiResponse\Tests;

use Vlancy\LaravelApiResponse\Exceptions\Handler;
use Vlancy\LaravelApiResponse\Providers\APIResponseProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app)
    {
        return [
            APIResponseProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Set up the response configuration
        $app['config']->set('api-response.removeNullDataValues', false);
        $app['config']->set('api-response.setNullEmptyData', true);
        $app['config']->set('api-response.returnValidationErrorsKeys', true);
        $app['config']->set('api-response.apiSuccessCodes', [200, 201, 202]);
        $app['config']->set('api-response.apiExceptionCodes', [409, 422, 400, 401, 403]);
        $app['config']->set('api-response.enableErrorCodes', true);
        $app['config']->set('api-response.errorCodes', \Vlancy\LaravelApiResponse\Enums\ErrorCodesEnum::class);
        $app['config']->set('api-response.errorCodesType', 'string');
        $app['config']->set('api-response.returnDefaultErrorCodes', true);
        $app['config']->set('api-response.hideMetaPaginationLinks', true);
    }

    /**
     * Resolve application HTTP exception handler.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationExceptionHandler($app)
    {
        $app->singleton('Illuminate\Contracts\Debug\ExceptionHandler', Handler::class);
    }
}
