<?php

namespace Maidomax\DatadogLogger\Tests;

use Maidomax\DatadogLogger\DatadogHttpHandler;
use Maidomax\DatadogLogger\DatadogLogger;
use Monolog\Logger;

class DatadogLoggerTest extends TestCase
{
    public function test_it_creates_logger_with_datadog_handler()
    {
        $config = [
            'api_key' => 'test-key',
            'intake_url' => 'https://example.com/logs',
            'service' => 'test-service',
            'environment' => 'test',
            'source' => 'test-source',
        ];

        $datadogLogger = new DatadogLogger();
        $logger = $datadogLogger($config);

        $this->assertInstanceOf(Logger::class, $logger);
        $this->assertEquals('datadog', $logger->getName());

        $handlers = $logger->getHandlers();
        $this->assertCount(1, $handlers);
        $this->assertInstanceOf(DatadogHttpHandler::class, $handlers[0]);
    }

    public function test_it_uses_environment_variables_as_fallback()
    {
        putenv('DATADOG_API_KEY=env-key');
        putenv('DATADOG_LOG_INTAKE_URL=https://env.example.com/logs');

        $config = [];
        $datadogLogger = new DatadogLogger();
        $logger = $datadogLogger($config);

        $this->assertInstanceOf(Logger::class, $logger);

        // Clean up
        putenv('DATADOG_API_KEY');
        putenv('DATADOG_LOG_INTAKE_URL');
    }

    public function test_logging_channel_is_registered()
    {
        $this->app['config']->set('logging.channels.datadog', [
            'driver' => 'datadog',
            'api_key' => 'test-key',
            'intake_url' => 'https://example.com/logs',
        ]);

        $logger = $this->app['log']->channel('datadog');

        $this->assertInstanceOf(\Illuminate\Log\Logger::class, $logger);
    }
}
