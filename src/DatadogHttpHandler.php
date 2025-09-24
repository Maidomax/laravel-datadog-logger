<?php

namespace Maidomax\DatadogLogger;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;

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
        $level = Level::Debug,
        bool $bubble = true
    ) {
        parent::__construct($level, $bubble);
        $this->apiKey = $apiKey;
        $this->intakeUrl = $intakeUrl;
        $this->service = $service;
        $this->environment = $environment;
        $this->source = $source;
        $this->timeout = $timeout;
        $this->connectionTimeout = $connectionTimeout;
    }

    protected function write(LogRecord $record): void
    {
        $logData = [
            'ddsource' => $this->source,
            'ddtags' => 'env:' . $this->environment . ',service:' . $this->service,
            'hostname' => gethostname(),
            'message' => $record->message,
            'level' => strtolower($record->level->name),
            'timestamp' => $record->datetime->getTimestamp() * 1000, // Datadog expects milliseconds
            'context' => $record->context,
            'extra' => $record->extra,
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
            if (strpos($statusLine, '2') !== 1) { // Not a 2xx status code
                error_log(sprintf(
                    '[DatadogLogger] Datadog API returned non-success status: %s',
                    $statusLine
                ));
            }
        }
    }
}
