<?php

namespace Maidomax\DatadogLogger\Tests;

use Maidomax\DatadogLogger\DatadogLoggerServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            DatadogLoggerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('datadog-logger.api_key', 'test-api-key');
        $app['config']->set('datadog-logger.intake_url', 'https://http-intake.logs.datadoghq.com/api/v2/logs');
        $app['config']->set('datadog-logger.service', 'test-service');
        $app['config']->set('datadog-logger.environment', 'testing');
    }
}
