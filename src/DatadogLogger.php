<?php

namespace Maidomax\DatadogLogger;

use Monolog\Formatter\JsonFormatter;
use Monolog\Logger;

class DatadogLogger
{
    public function __invoke(array $config)
    {
        $logger = new Logger('datadog');

        $handler = new DatadogHttpHandler(
            $config['api_key'] ?? env('DATADOG_API_KEY'),
            $config['intake_url'] ?? env('DATADOG_LOG_INTAKE_URL', 'https://http-intake.logs.datadoghq.com/api/v2/logs'),
            $config['service'] ?? env('DATADOG_SERVICE', config('app.name', 'laravel')),
            $config['environment'] ?? env('DATADOG_ENVIRONMENT', app()->environment()),
            $config['source'] ?? env('DATADOG_SOURCE', env('APP_NAME', 'laravel')),
            $config['timeout'] ?? env('DATADOG_TIMEOUT', 5),
            $config['connection_timeout'] ?? env('DATADOG_CONNECTION_TIMEOUT', 3),
            Logger::DEBUG
        );

        $formatter = new JsonFormatter();
        $handler->setFormatter($formatter);

        $logger->pushHandler($handler);

        return $logger;
    }
}
