<?php

namespace Bdmotaleb\EventApiClient\Tests;

use Bdmotaleb\EventApiClient\EventsClient;
use PHPUnit\Framework\TestCase;

class EventsClientTest extends TestCase
{
    public function testConstructorWithValidParameters()
    {
        $client = new EventsClient(
            'https://api.example.com',
            'test-project-key',
            'test-access-token'
        );
        
        $this->assertInstanceOf(EventsClient::class, $client);
    }

    public function testConstructorThrowsExceptionForEmptyBaseUrl()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Base URL cannot be empty');
        
        new EventsClient('', 'test-project-key', 'test-access-token');
    }

    public function testConstructorThrowsExceptionForEmptyProjectKey()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Project key cannot be empty');
        
        new EventsClient('https://api.example.com', '', 'test-access-token');
    }

    public function testConstructorThrowsExceptionForEmptyAccessToken()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Access token cannot be empty');
        
        new EventsClient('https://api.example.com', 'test-project-key', '');
    }

    public function testConstructorWithOptions()
    {
        $client = new EventsClient(
            'https://api.example.com',
            'test-project-key',
            'test-access-token',
            [
                'maxRetries' => 5,
                'timeoutMs' => 15000
            ]
        );
        
        $this->assertInstanceOf(EventsClient::class, $client);
    }
}
