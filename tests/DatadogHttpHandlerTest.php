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
            Level::Info,
            false
        );

        $this->assertInstanceOf(DatadogHttpHandler::class, $handler);
    }

    public function test_write_method_creates_proper_log_structure()
    {
        $handler = new class('test-key', 'https://example.com/logs') extends DatadogHttpHandler {
            public $lastLogData;

            protected function sendToDatadog(array $logData): void
            {
                $this->lastLogData = $logData;
            }
        };

        $logRecord = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Info,
            message: 'Test message',
            context: ['key' => 'value'],
            extra: []
        );

        $reflectionMethod = new \ReflectionMethod($handler, 'write');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($handler, $logRecord);

        $this->assertArrayHasKey('ddsource', $handler->lastLogData);
        $this->assertArrayHasKey('ddtags', $handler->lastLogData);
        $this->assertArrayHasKey('hostname', $handler->lastLogData);
        $this->assertArrayHasKey('message', $handler->lastLogData);
        $this->assertArrayHasKey('level', $handler->lastLogData);
        $this->assertArrayHasKey('timestamp', $handler->lastLogData);
        $this->assertArrayHasKey('context', $handler->lastLogData);
        $this->assertArrayHasKey('extra', $handler->lastLogData);

        $this->assertEquals('laravel', $handler->lastLogData['ddsource']);
        $this->assertEquals('Test message', $handler->lastLogData['message']);
        $this->assertEquals('info', $handler->lastLogData['level']);
        $this->assertEquals(['key' => 'value'], $handler->lastLogData['context']);
    }

    public function test_write_method_uses_custom_source()
    {
        $handler = new class('test-key', 'https://example.com/logs', 'test-service', 'test-env', 'custom-source') extends DatadogHttpHandler {
            public $lastLogData;

            protected function sendToDatadog(array $logData): void
            {
                $this->lastLogData = $logData;
            }
        };

        $logRecord = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Info,
            message: 'Test message',
            context: [],
            extra: []
        );

        $reflectionMethod = new \ReflectionMethod($handler, 'write');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($handler, $logRecord);

        $this->assertEquals('custom-source', $handler->lastLogData['ddsource']);
        $this->assertEquals('test-service', explode(',', str_replace(['env:', 'service:'], '', $handler->lastLogData['ddtags']))[1]);
    }
}