<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Datadog API Key
    |--------------------------------------------------------------------------
    |
    | Your Datadog API key used for authentication when sending logs.
    | You can find this in your Datadog account settings.
    |
    */
    'api_key' => env('DATADOG_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Datadog Log Intake URL
    |--------------------------------------------------------------------------
    |
    | The Datadog HTTP log intake endpoint. Use the appropriate URL for your region:
    | - US: https://http-intake.logs.datadoghq.com/api/v2/logs
    | - EU: https://http-intake.logs.datadoghq.eu/api/v2/logs
    | - US3: https://http-intake.logs.us3.datadoghq.com/api/v2/logs
    | - US5: https://http-intake.logs.us5.datadoghq.com/api/v2/logs
    |
    */
    'intake_url' => env('DATADOG_LOG_INTAKE_URL', 'https://http-intake.logs.datadoghq.com/api/v2/logs'),

    /*
    |--------------------------------------------------------------------------
    | Service Name
    |--------------------------------------------------------------------------
    |
    | The service name to be used in Datadog logs. This helps identify
    | which service the logs are coming from.
    |
    */
    'service' => env('DATADOG_SERVICE', config('app.name', 'laravel')),

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | The environment name to be used in Datadog logs (e.g., production, staging, local).
    |
    */
    'environment' => env('DATADOG_ENVIRONMENT', app()->environment()),

    /*
    |--------------------------------------------------------------------------
    | Data Source
    |--------------------------------------------------------------------------
    |
    | The data source identifier used in Datadog logs. This helps identify
    | the type/source of the logs in Datadog. Defaults to APP_NAME, then 'laravel'.
    |
    */
    'source' => env('DATADOG_SOURCE', env('APP_NAME', 'laravel')),

    /*
    |--------------------------------------------------------------------------
    | Log Level
    |--------------------------------------------------------------------------
    |
    | The minimum log level that will be sent to Datadog.
    | Available levels: emergency, alert, critical, error, warning, notice, info, debug
    |
    */
    'level' => env('DATADOG_LOG_LEVEL', 'debug'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Timeout
    |--------------------------------------------------------------------------
    |
    | Timeout in seconds for HTTP requests to Datadog.
    | Lower values prevent blocking but may cause log loss on slow networks.
    |
    */
    'timeout' => env('DATADOG_TIMEOUT', 5),

    /*
    |--------------------------------------------------------------------------
    | Connection Timeout
    |--------------------------------------------------------------------------
    |
    | Connection timeout in seconds for establishing connection to Datadog.
    |
    */
    'connection_timeout' => env('DATADOG_CONNECTION_TIMEOUT', 3),
];
