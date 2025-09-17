## bdmotaleb/event-api-client (PHP)

Minimal PHP client for sending events to external API. Uses ext-curl. No extra dependencies.

### Install
```bash
composer require bdmotaleb/event-api-client
```

### Configuration
- **Env variables** (recommended):
  - `EVENTS_BASE_URL` (default `http://127.0.0.1:8000/api/v1`)
  - `PROJECT_KEY`
  - `ACCESS_TOKEN`

Ensure PHP extensions are enabled: `ext-curl`, `ext-json`.

After installing, if you add this SDK to an existing app, run:
```bash
composer dump-autoload
```

### Usage
```php
<?php
use Bdmotaleb\EventApiClient\EventsClient;
use Bdmotaleb\EventApiClient\Events;

$client = new EventsClient(
    'https://your-api.example.com',
    getenv('PROJECT_KEY'),
    getenv('ACCESS_TOKEN'),
    ['maxRetries' => 3]
);

$resp = $client->trackEvent([
    'event_type' => 'bidash-visualization',
    'timestamp'  => now()->toIso8601String(),
    'platform'   => 'web',
    'details'    => [
        'feature' => 'Payroll Processing',
        'manual_operation_time_minutes' => 120,
        'saved_time_minutes' => 90,
        'role' => 'HR Manager',
    ],
]);

print_r($resp);
```

#### Simplified global usage (Laravel-style)
```php
<?php
use Bdmotaleb\EventApiClient\Events;

// Optional: configure once (or rely on env: EVENTS_BASE_URL, PROJECT_KEY, ACCESS_TOKEN)
Events::configure([
    'baseUrl' => env('EVENTS_BASE_URL', 'http://127.0.0.1:8000/api/v1'),
    'projectKey' => env('PROJECT_KEY'),
    'accessToken' => env('ACCESS_TOKEN'),
    'clientOptions' => ['maxRetries' => 3],
]);

// Then anywhere
return Events::track([
    'event_type' => 'bidash-visualization',
    'timestamp'  => now()->toIso8601String(),
    'platform'   => 'web',
    'details'    => [
        'feature' => 'Payroll Processing',
        'manual_operation_time_minutes' => 120,
        'saved_time_minutes' => 90,
        'role' => 'HR Manager',
    ],
]);
```

#### One-liner helper functions
After `composer dump-autoload`, you can use global helpers:
```php
// Optional configuration (or rely on env)
events_config([
    'baseUrl' => env('EVENTS_BASE_URL', 'http://127.0.0.1:8000/api/v1'),
    'projectKey' => env('PROJECT_KEY'),
    'accessToken' => env('ACCESS_TOKEN'),
]);

// Track in one line
return events_track([
    'event_type' => 'bidash-visualization',
    'timestamp'  => now()->toIso8601String(),
    'platform'   => 'web',
    'details'    => [
        'feature' => 'Payroll Processing',
        'manual_operation_time_minutes' => 120,
        'saved_time_minutes' => 90,
        'role' => 'HR Manager',
    ],
]);
```
### Error handling
- On success: returns decoded JSON from the API.
- On API errors (e.g., invalid credentials 401): returns the server body as-is if JSON.
- If the server does not return JSON, you get a standardized structure:
```json
{"status":"failed","code":401,"data":null,"message":"...","errors":null}
```

Example Laravel route returning errors directly from the API client:
```php
use Bdmotaleb\EventApiClient\Events;

Route::get('/test', function () {
    return Events::track([
        'event_type' => 'bidash-visualization',
        'timestamp'  => now()->toIso8601String(),
        'platform'   => 'web',
        'details'    => [
            'feature' => 'Payroll Processing',
            'manual_operation_time_minutes' => 120,
            'saved_time_minutes' => 90,
            'role' => 'HR Manager',
        ],
    ]);
});
```

### Options
- maxRetries (default 3)
- initialDelayMs (default 400)
- backoffFactor (default 2.0)
- maxDelayMs (default 8000)
- timeoutMs (default 10000)


