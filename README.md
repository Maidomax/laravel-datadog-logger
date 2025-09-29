# Laravel Datadog Logger

A Laravel package that provides seamless integration with Datadog's HTTP log intake API, allowing you to send your Laravel application logs directly to Datadog for centralized logging and monitoring.

## Features

- 🚀 Easy integration with Laravel's logging system
- 🌍 Support for all Datadog regions (US, EU, US3, US5)
- 🏷️ Automatic tagging with service name and environment
- ⚡ HTTP-based log transmission to Datadog
- 🔧 Configurable via environment variables or config file
- 📦 Auto-discovery support for Laravel 5.5+

## Installation

You can install the package via Composer:

```bash
composer require maidomax/laravel-datadog-logger
```

### Laravel Auto-Discovery

For Laravel 5.5+ with package auto-discovery enabled, the service provider will be automatically registered. For older versions, add the service provider to your `config/app.php`:

```php
'providers' => [
    // ...
    Maidomax\DatadogLogger\DatadogLoggerServiceProvider::class,
],
```

### Publish Configuration (Optional)

You can publish the configuration file to customize the package settings:

```bash
php artisan vendor:publish --tag=datadog-logger-config
```

This will create a `config/datadog-logger.php` file where you can customize the package settings.

## Configuration

### Environment Variables

Add the following environment variables to your `.env` file:

```env
# Required: Your Datadog API Key
DATADOG_API_KEY=your_datadog_api_key_here

# Required: Datadog Log Intake URL for your region
# US: https://http-intake.logs.datadoghq.com/api/v2/logs
# EU: https://http-intake.logs.datadoghq.eu/api/v2/logs
# US3: https://http-intake.logs.us3.datadoghq.com/api/v2/logs
# US5: https://http-intake.logs.us5.datadoghq.com/api/v2/logs
DATADOG_LOG_INTAKE_URL=https://http-intake.logs.datadoghq.com/api/v2/logs

# Optional: Service name (defaults to 'laravel')
DATADOG_SERVICE=my-laravel-app

# Optional: Environment (defaults to APP_ENV, then 'local')
DATADOG_ENVIRONMENT=production

# Optional: Data source identifier (defaults to 'laravel')
DATADOG_SOURCE=my-custom-source

# Optional: HTTP timeout in seconds (defaults to 5)
DATADOG_TIMEOUT=10

# Optional: Connection timeout in seconds (defaults to 3)
DATADOG_CONNECTION_TIMEOUT=5
```

### Laravel Logging Configuration

Add the Datadog logging channel to your `config/logging.php`:

```php
'channels' => [
    // ... other channels

    'datadog' => [
        'driver' => 'datadog',
        'api_key' => env('DATADOG_API_KEY'),
        'intake_url' => env('DATADOG_LOG_INTAKE_URL'),
        'service' => env('DATADOG_SERVICE'),
        'environment' => env('DATADOG_ENVIRONMENT'),
        'source' => env('DATADOG_SOURCE'),
        'timeout' => env('DATADOG_TIMEOUT', 5),
        'connection_timeout' => env('DATADOG_CONNECTION_TIMEOUT', 3),
    ],

    // Example: Use Datadog alongside other logging channels
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single', 'datadog'],
        'ignore_exceptions' => false,
    ],
],
```

### Set Default Log Channel

Update your `.env` file to use the stack channel (or directly use datadog):

```env
LOG_CHANNEL=stack
```

## Usage

Once configured, you can use Laravel's standard logging methods and they will be sent to Datadog:

```php
use Illuminate\Support\Facades\Log;

// These will be sent to Datadog (and other configured channels)
Log::emergency('System is down!');
Log::alert('High CPU usage detected');
Log::critical('Database connection failed');
Log::error('User authentication failed', ['user_id' => 123]);
Log::warning('Deprecated API endpoint used');
Log::notice('User logged in', ['user_id' => 456]);
Log::info('Order processed successfully', ['order_id' => 789]);
Log::debug('Debug information', ['data' => $someData]);

// Using specific channel
Log::channel('datadog')->info('This goes directly to Datadog');

// With context
Log::info('User action performed', [
    'user_id' => 123,
    'action' => 'purchase',
    'amount' => 99.99,
    'ip_address' => request()->ip(),
]);
```

## Log Structure

Logs sent to Datadog will include the following fields:

- `ddsource`: Data source identifier (configurable via `DATADOG_SOURCE`, defaults to "laravel")
- `ddtags`: Environment and service tags (e.g., "env:production,service:my-app")
- `hostname`: Server hostname
- `message`: The log message
- `level`: Log level (emergency, alert, critical, error, warning, notice, info, debug)
- `timestamp`: Unix timestamp in milliseconds
- `context`: Additional context data passed to the log
- `extra`: Extra data from Monolog processors

## Datadog Regions

This package supports all Datadog regions. Make sure to use the correct intake URL for your region:

| Region | Intake URL |
|--------|------------|
| US | `https://http-intake.logs.datadoghq.com/api/v2/logs` |
| EU | `https://http-intake.logs.datadoghq.eu/api/v2/logs` |
| US3 | `https://http-intake.logs.us3.datadoghq.com/api/v2/logs` |
| US5 | `https://http-intake.logs.us5.datadoghq.com/api/v2/logs` |

## Error Handling

If the package fails to send logs to Datadog (e.g., network issues, invalid API key), it will:

1. Not throw exceptions to avoid breaking your application
2. Log the error to PHP's error log for debugging
3. Continue processing other log channels in your stack

## Performance Considerations

- Log transmission to Datadog is performed synchronously via HTTP requests
- Failed requests have a configurable timeout (default 5 seconds) to prevent hanging
- For high-volume applications or to avoid blocking, consider using a queue-based logging solution

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email maidomax@yahoo.com instead of using the issue tracker.

## Credits

- [Maidomax](https://github.com/Maidomax)

## License

The MIT License (MIT). Please see [LICENSE.md](LICENSE.md) for more information.

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for more information on what has changed recently.