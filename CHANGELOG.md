# Changelog

All notable changes to `laravel-datadog-logger` will be documented in this file.

## [1.0.0] - 2025-09-24

### Added
- Initial release of Laravel Datadog Logger package
- Support for sending Laravel logs to Datadog via HTTP API
- Configurable service provider with auto-discovery support
- Support for all Datadog regions (US, EU, US3, US5)
- Automatic tagging with environment and service information
- Asynchronous log transmission to avoid blocking application
- Comprehensive test suite with PHPUnit
- Full documentation and usage examples
- GitHub Actions CI/CD pipeline for automated testing
- PHP-CS-Fixer integration for code style consistency

### Features
- **Custom Monolog Handler**: Seamless integration with Laravel's logging system
- **Configurable Data Source**: Set custom `ddsource` via environment variables with intelligent fallbacks
- **HTTP Timeout Configuration**: Configurable timeouts to prevent blocking
- **Enhanced Error Handling**: Detailed error logging with HTTP status checking
- **SSL Security**: Proper SSL certificate verification
- **JSON Optimization**: UTF-8 and slash handling for international logs
- **Environment Variable Support**: Complete configuration via `.env` file
- **Publishable Configuration**: Optional config file publishing
- **Multi-Laravel Support**: Laravel 9.x, 10.x, 11.x, and 12.x compatibility
- **PHP 8.0+ Compatibility**: Support for modern PHP versions
- **Auto-Discovery**: Automatic service provider registration in Laravel 5.5+

### Configuration Options
- `DATADOG_API_KEY`: Your Datadog API key
- `DATADOG_LOG_INTAKE_URL`: Regional intake endpoint
- `DATADOG_SERVICE`: Service name (defaults to app name)
- `DATADOG_ENVIRONMENT`: Environment identifier
- `DATADOG_SOURCE`: Data source (defaults to APP_NAME, then 'laravel')
- `DATADOG_TIMEOUT`: HTTP request timeout (default: 5s)
- `DATADOG_CONNECTION_TIMEOUT`: Connection timeout (default: 3s)
- `DATADOG_LOG_LEVEL`: Minimum log level (default: debug)