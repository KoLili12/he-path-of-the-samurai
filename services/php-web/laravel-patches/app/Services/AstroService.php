<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AstroService
{
    private string $astroAppId;
    private string $astroAppSecret;
    private string $astroBaseUrl = 'https://api.astronomyapi.com/api/v2';
    private int $cacheTtl = 3600; // 1 hour
    private int $timeout = 15;

    public function __construct()
    {
        $this->astroAppId = env('ASTRO_APP_ID', '');
        $this->astroAppSecret = env('ASTRO_APP_SECRET', '');
    }

    /**
     * Get astronomy events with caching
     *
     * @param string|null $fromDate
     * @param string|null $toDate
     * @param string|null $eventType
     * @param int $limit
     * @return array
     */
    public function getEvents(
        ?string $fromDate = null,
        ?string $toDate = null,
        ?string $eventType = null,
        int $limit = 10
    ): array {
        $cacheKey = $this->buildCacheKey('events', $fromDate, $toDate, $eventType, $limit);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($fromDate, $toDate, $eventType, $limit) {
            return $this->fetchEvents($fromDate, $toDate, $eventType, $limit);
        });
    }

    /**
     * Fetch astronomy events from API
     *
     * @param string|null $fromDate
     * @param string|null $toDate
     * @param string|null $eventType
     * @param int $limit
     * @return array
     */
    private function fetchEvents(
        ?string $fromDate,
        ?string $toDate,
        ?string $eventType,
        int $limit
    ): array {
        if (empty($this->astroAppId) || empty($this->astroAppSecret)) {
            return $this->getErrorResponse(401, 'Astronomy API credentials not configured');
        }

        try {
            $params = [
                'from_date' => $fromDate ?? now()->format('Y-m-d'),
                'to_date' => $toDate ?? now()->addDays(30)->format('Y-m-d'),
            ];

            if ($eventType) {
                $params['type'] = $eventType;
            }

            $response = Http::timeout($this->timeout)
                ->withBasicAuth($this->astroAppId, $this->astroAppSecret)
                ->retry(2, 200)
                ->get($this->astroBaseUrl . '/studio/star-chart', $params);

            if ($response->successful()) {
                $data = $response->json() ?? [];
                return [
                    'ok' => true,
                    'data' => array_slice($data['data'] ?? [], 0, $limit),
                    'count' => count($data['data'] ?? []),
                ];
            }

            \Log::warning('Astronomy API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $this->getErrorResponse($response->status(), 'Astronomy API returned an error');
        } catch (\Exception $e) {
            \Log::error('Astronomy API exception', [
                'error' => $e->getMessage(),
            ]);

            return $this->getErrorResponse(500, 'Failed to connect to Astronomy API');
        }
    }

    /**
     * Build cache key
     *
     * @param string $prefix
     * @param mixed ...$params
     * @return string
     */
    private function buildCacheKey(string $prefix, ...$params): string
    {
        return 'astro:' . $prefix . ':' . md5(json_encode($params));
    }

    /**
     * Get error response in unified format
     *
     * @param int $statusCode
     * @param string $message
     * @return array
     */
    private function getErrorResponse(int $statusCode, string $message): array
    {
        return [
            'ok' => false,
            'error' => [
                'code' => 'ASTRONOMY_API_ERROR_' . $statusCode,
                'message' => $message,
                'trace_id' => uniqid('trace_', true),
            ],
        ];
    }
}
