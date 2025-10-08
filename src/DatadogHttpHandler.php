<?php

namespace Maidomax\DatadogLogger;

use Monolog\Handler\AbstractProcessingHandler;

// Conditionally use LogRecord for Monolog 3.x compatibility
if (class_exists(\Monolog\LogRecord::class)) {
    class_alias(\Monolog\LogRecord::class, 'LogRecordAlias');
}

class DatadogHttpHandler extends AbstractProcessingHandler
{
    private string $apiKey;
    private string $intakeUrl;
    private string $service;
    private string $environment;
    private string $source;
    private int $timeout;
    private int $connectionTimeout;

    public function __construct(
        string $apiKey,
        string $intakeUrl,
        string $service = 'laravel',
        string $environment = 'production',
        string $source = 'laravel',
        int $timeout = 5,
        int $connectionTimeout = 3,
        $level = null,
        bool $bubble = true
    ) {
        // Handle default level for both Monolog 2.x and 3.x
        if ($level === null) {
            if (class_exists(\Monolog\Level::class)) {
                // Monolog 3.x
                $level = \Monolog\Level::Debug;
            } else {
                // Monolog 2.x - use Logger constants
                $level = \Monolog\Logger::DEBUG;
            }
        }
        parent::__construct($level, $bubble);
        $this->apiKey = $apiKey;
        $this->intakeUrl = $intakeUrl;
        $this->service = $service;
        $this->environment = $environment;
        $this->source = $source;
        $this->timeout = $timeout;
        $this->connectionTimeout = $connectionTimeout;
    }

    protected function write($record): void
    {
        // Handle both Monolog 2.x (array) and 3.x (LogRecord object) formats
        if (class_exists(\Monolog\LogRecord::class) && $record instanceof \Monolog\LogRecord) {
            // Monolog 3.x
            $message = $record->message;
            $level = strtolower($record->level->name);
            $timestamp = $record->datetime->getTimestamp() * 1000;
            $context = $record->context;
            $extra = $record->extra;
        } else {
            // Monolog 2.x - $record is an array
            $message = $record['message'] ?? '';
            $level = strtolower($record['level_name'] ?? '');
            $timestamp = ($record['datetime'] ?? new \DateTimeImmutable())->getTimestamp() * 1000;
            $context = $record['context'] ?? [];
            $extra = $record['extra'] ?? [];
        }

        $logData = [
            'ddsource' => $this->source,
            'ddtags' => 'env:' . $this->environment . ',service:' . $this->service,
            'hostname' => gethostname(),
            'message' => $message,
            'level' => $level,
            'timestamp' => $timestamp,
            'context' => $context,
            'extra' => $extra,
        ];

        $this->sendToDatadog($logData);
    }

    private function sendToDatadog(array $logData): void
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Content-Type: application/json',
                    'DD-API-KEY: ' . $this->apiKey,
                    'User-Agent: maidomax/laravel-datadog-logger',
                ],
                'content' => json_encode($logData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'timeout' => $this->timeout,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
            ],
            'socket' => [
                'timeout' => $this->connectionTimeout,
            ],
        ]);

        // Send async to avoid blocking the application
        $result = @file_get_contents($this->intakeUrl, false, $context);

        // Enhanced error handling
        if ($result === false) {
            $error = error_get_last();
            $errorMessage = $error['message'] ?? 'Unknown error';

            // Log detailed error information for debugging
            error_log(sprintf(
                '[DatadogLogger] Failed to send log to Datadog (%s): %s - Data: %s',
                $this->intakeUrl,
                $errorMessage,
                json_encode($logData, JSON_UNESCAPED_SLASHES)
            ));
        }

        // Check HTTP response status if available
        if (isset($http_response_header)) {
            $statusLine = $http_response_header[0] ?? '';
            // Extract status code from "HTTP/1.1 200 OK" or "HTTP/1.1 200" format
            if (preg_match('/\s(\d{3})(?:\s|$)/', $statusLine, $matches)) {
                $statusCode = (int)$matches[1];
                if ($statusCode < 200 || $statusCode >= 300) { // Not a 2xx status code
                    error_log(sprintf(
                        '[DatadogLogger] Datadog API returned non-success status: %s',
                        $statusLine
                    ));
                }
            }
        }
    }
}
