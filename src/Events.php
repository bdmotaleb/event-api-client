<?php

namespace Bdmotaleb\EventApiClient;

class Events
{
    private static ?EventsClient $client = null;

    /**
     * Configure a shared client instance. If not called, env vars are used lazily.
     * options: [
     *   'baseUrl' => string,
     *   'projectKey' => string,
     *   'accessToken' => string,
     *   'clientOptions' => array
     * ]
     */
    public static function configure(array $options = []): void
    {
        $baseUrl = $options['baseUrl'] ?? (getenv('EVENTS_BASE_URL') ?: 'http://127.0.0.1:8000/api/v1');
        $projectKey = $options['projectKey'] ?? (getenv('PROJECT_KEY') ?: '');
        $accessToken = $options['accessToken'] ?? (getenv('ACCESS_TOKEN') ?: '');
        $clientOptions = $options['clientOptions'] ?? [];

        self::$client = new EventsClient($baseUrl, $projectKey, $accessToken, $clientOptions);
    }

    /**
     * Manually set the shared client instance.
     */
    public static function using(EventsClient $client): void
    {
        self::$client = $client;
    }

    private static function getClient(): EventsClient
    {
        if (self::$client === null) {
            self::configure();
        }
        return self::$client;
    }

    /**
     * Shorthand similar to Log::info() style usage.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public static function track(array $payload): array
    {
        return self::getClient()->trackEvent($payload);
    }

    /**
     * Explicit method name kept for symmetry with the client.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public static function trackEvent(array $payload): array
    {
        return self::getClient()->trackEvent($payload);
    }
}


