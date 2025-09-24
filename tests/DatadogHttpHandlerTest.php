<?php

namespace Maidomax\DatadogLogger\Tests;

use Maidomax\DatadogLogger\DatadogHttpHandler;
use PHPUnit\Framework\TestCase;

class DatadogHttpHandlerTest extends TestCase
{
    public function test_it_can_be_constructed_with_required_parameters()
    {
        $handler = new DatadogHttpHandler(
            'test-api-key',
            'https://example.com/logs'
        );

        $this->assertInstanceOf(DatadogHttpHandler::class, $handler);
    }

    public function test_it_can_be_constructed_with_all_parameters()
    {
        // Use level constants that work with both Monolog 2.x and 3.x
        $level = class_exists(\Monolog\Level::class)
            ? \Monolog\Level::Info
            : \Monolog\Logger::INFO;

        $handler = new DatadogHttpHandler(
            'test-api-key',
            'https://example.com/logs',
            'test-service',
            'testing',
            'test-source',
            5,
            3,
            $level,
            false
        );

        $this->assertInstanceOf(DatadogHttpHandler::class, $handler);
    }

    public function test_handler_can_be_used_with_logger()
    {
        $handler = new DatadogHttpHandler(
            'test-api-key',
            'https://example.com/logs',
            'test-service',
            'test-env',
            'test-source'
        );

        // Test that handler can be created and used without errors
        $this->assertInstanceOf(DatadogHttpHandler::class, $handler);

        // Test basic functionality - create log record compatible with both Monolog versions
        if (class_exists(\Monolog\LogRecord::class)) {
            // Monolog 3.x
            $logRecord = new \Monolog\LogRecord(
                datetime: new \DateTimeImmutable(),
                channel: 'test',
                level: \Monolog\Level::Info,
                message: 'Test message',
                context: ['key' => 'value'],
                extra: []
            );
        } else {
            // Monolog 2.x - use array format
            $logRecord = [
                'datetime' => new \DateTimeImmutable(),
                'channel' => 'test',
                'level' => \Monolog\Logger::INFO,
                'level_name' => 'INFO',
                'message' => 'Test message',
                'context' => ['key' => 'value'],
                'extra' => [],
            ];
        }

        // Should not throw exception
        $handler->handle($logRecord);
        $this->assertTrue(true); // If we get here without exception, test passes
    }

    public function test_handler_accepts_all_constructor_parameters()
    {
        // Use level constants that work with both Monolog 2.x and 3.x
        $level = class_exists(\Monolog\Level::class)
            ? \Monolog\Level::Warning
            : \Monolog\Logger::WARNING;

        // Test that all constructor parameters are accepted
        $handler = new DatadogHttpHandler(
            'test-key',
            'https://example.com/logs',
            'test-service',
            'test-env',
            'custom-source',
            10,
            5,
            $level,
            false
        );

        $this->assertInstanceOf(DatadogHttpHandler::class, $handler);
    }
}
