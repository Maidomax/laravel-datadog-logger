<?php

namespace Maidomax\DatadogLogger\Tests;

use Maidomax\DatadogLogger\DatadogHttpHandler;
use Monolog\Level;
use Monolog\LogRecord;
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
        $handler = new DatadogHttpHandler(
            'test-api-key',
            'https://example.com/logs',
            'test-service',
            'testing',
            'test-source',
            5,
            3,
            Level::Info,
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

        // Test basic functionality by checking it implements the right interface
        $logRecord = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Info,
            message: 'Test message',
            context: ['key' => 'value'],
            extra: []
        );

        // Should not throw exception
        $handler->handle($logRecord);
        $this->assertTrue(true); // If we get here without exception, test passes
    }

    public function test_handler_accepts_all_constructor_parameters()
    {
        // Test that all constructor parameters are accepted
        $handler = new DatadogHttpHandler(
            'test-key',
            'https://example.com/logs',
            'test-service',
            'test-env',
            'custom-source',
            10,
            5,
            Level::Warning,
            false
        );

        $this->assertInstanceOf(DatadogHttpHandler::class, $handler);
    }
}
