<?php

namespace Bdmotaleb\EventApiClient;

class EventsClient
{
    private string $baseUrl;
    private string $projectKey;
    private string $accessToken;
    private int $maxRetries;
    private int $initialDelayMs;
    private float $backoffFactor;
    private int $maxDelayMs;
    private int $timeoutMs;

    public function __construct(
        string $baseUrl,
        string $projectKey,
        string $accessToken,
        array $options = []
    ) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->projectKey = $projectKey;
        $this->accessToken = $accessToken;
        $this->maxRetries = $options['maxRetries'] ?? 3;
        $this->initialDelayMs = $options['initialDelayMs'] ?? 400;
        $this->backoffFactor = $options['backoffFactor'] ?? 2.0;
        $this->maxDelayMs = $options['maxDelayMs'] ?? 8000;
        $this->timeoutMs = $options['timeoutMs'] ?? 10000;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function trackEvent(array $payload): array
    {
        $url = $this->baseUrl . '/events';
        $payload = array_merge([
            'occurred_at' => gmdate('c'),
        ], $payload);

        return $this->requestWithRetry($url, 'POST', json_encode($payload, JSON_UNESCAPED_SLASHES));
    }

    /**
     * @return array<string, mixed>
     */
    private function requestWithRetry(string $url, string $method, ?string $body = null): array
    {
        $attempt = 0;
        $delay = $this->initialDelayMs;

        while (true) {
            $ch = curl_init();
            $headers = [
                'X-Project-Key: ' . $this->projectKey,
                'X-Access-Token: ' . $this->accessToken,
                'Content-Type: application/json',
                'Accept: application/json',
            ];

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT_MS => $this->timeoutMs,
            ]);
            if ($body !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            }

            $responseBody = curl_exec($ch);
            $errno = curl_errno($ch);
            $error = curl_error($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($errno !== 0) {
                // Network or timeout error
                if ($attempt >= $this->maxRetries) {
                    return [
                        'status' => 'failed',
                        'code' => 0,
                        'data' => null,
                        'message' => 'Network error: ' . ($error ?: 'Unknown cURL error'),
                        'errors' => null,
                    ];
                }
            } else {
                if ($status >= 200 && $status < 300) {
                    $decoded = json_decode($responseBody ?: 'null', true);
                    if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
                        return [
                            'status' => 'failed',
                            'code' => $status,
                            'data' => null,
                            'message' => 'Invalid JSON response from server',
                            'errors' => null,
                        ];
                    }
                    return $decoded ?? [];
                }

                $retryable = $status === 429 || $status >= 500;
                if (!$retryable || $attempt >= $this->maxRetries) {
                    // Try to parse response body as JSON with the server's structure
                    $decodedError = null;
                    if (is_string($responseBody) && $responseBody !== '') {
                        $decodedError = json_decode($responseBody, true);
                    }

                    if (is_array($decodedError)) {
                        // Assume server already returns structured error
                        return $decodedError;
                    }

                    $message = is_string($responseBody) && $responseBody !== ''
                        ? trim($responseBody)
                        : 'HTTP error';

                    return [
                        'status' => 'failed',
                        'code' => $status,
                        'data' => null,
                        'message' => $message,
                        'errors' => null,
                    ];
                }
            }

            // Backoff
            $attempt += 1;
            $jitter = (mt_rand(875, 1125)) / 1000.0; // +/- ~12.5%
            $sleepMs = (int) min($delay * $jitter, $this->maxDelayMs);
            usleep($sleepMs * 1000);
            $delay = (int) min($delay * $this->backoffFactor, $this->maxDelayMs);
        }
    }
}


