<?php

use Bdmotaleb\EventApiClient\Events;

if (!function_exists('events_config')) {
    /**
     * Configure the shared Events client (optional; defaults use env vars).
     */
    function events_config(array $options = []): void
    {
        Events::configure($options);
    }
}

if (!function_exists('events_track')) {
    /**
     * Track an event with a single function call.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    function events_track(array $payload): array
    {
        return Events::track($payload);
    }
}


