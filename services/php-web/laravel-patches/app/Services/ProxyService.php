<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ProxyService
{
    private string $rustBaseUrl;
    private int $cacheTtl = 60; // 1 minute for ISS data
    private int $timeout = 10; // 10 seconds timeout

    public function __construct()
    {
        $this->rustBaseUrl = env('RUST_BASE', 'http://rust_iss:3000');
    }

    /**
     * Get last ISS position with caching
     *
     * @return array
     */
    public function getLastIssPosition(): array
    {
        return Cache::remember('iss:last_position', $this->cacheTtl, function () {
            return $this->fetchFromRustApi('/last');
        });
    }

    /**
     * Get ISS trend data with caching
     *
     * @param int $limit
     * @return array
     */
    public function getIssTrend(int $limit = 100): array
    {
        $cacheKey = "iss:trend:{$limit}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($limit) {
            return $this->fetchFromRustApi('/trend', ['limit' => $limit]);
        });
    }

    /**
     * Get OSDR datasets
     *
     * @param array $params
     * @return array
     */
    public function getOsdrDatasets(array $params = []): array
    {
        $cacheKey = 'osdr:datasets:' . md5(json_encode($params));

        return Cache::remember($cacheKey, 300, function () use ($params) {
            return $this->fetchFromRustApi('/osdr/datasets', $params);
        });
    }

    /**
     * Fetch data from Rust API
     *
     * @param string $endpoint
     * @param array $params
     * @return array
     */
    private function fetchFromRustApi(string $endpoint, array $params = []): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->retry(3, 100) // 3 retries with 100ms delay
                ->get($this->rustBaseUrl . $endpoint, $params);

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            \Log::warning('Rust API error', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $this->getErrorResponse($response->status(), 'Rust API returned an error');
        } catch (\Exception $e) {
            \Log::error('Rust API exception', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            return $this->getErrorResponse(500, 'Failed to connect to Rust API');
        }
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
                'code' => 'UPSTREAM_ERROR_' . $statusCode,
                'message' => $message,
                'trace_id' => uniqid('trace_', true),
            ],
        ];
    }

    /**
     * Check Rust API health
     *
     * @return bool
     */
    public function checkHealth(): bool
    {
        try {
            $response = Http::timeout(5)->get($this->rustBaseUrl . '/health');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
