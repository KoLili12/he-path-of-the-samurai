<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class ProxyController extends Controller
{
    private function base(): string {
        return getenv('RUST_BASE') ?: 'http://rust_iss:3000';
    }

    public function last()  { return $this->pipe('/last'); }

    public function trend() {
        $limit = (int) request()->get('limit', 240);
        $limit = max(1, min($limit, 1000)); // Limit between 1 and 1000

        try {
            $rows = \DB::select("
                SELECT
                    fetched_at,
                    payload->>'latitude' as lat,
                    payload->>'longitude' as lon,
                    (payload->>'velocity')::numeric as velocity,
                    (payload->>'altitude')::numeric as altitude
                FROM iss_fetch_log
                ORDER BY fetched_at DESC
                LIMIT ?
            ", [$limit]);

            $points = array_map(function($row) {
                return [
                    'at' => $row->fetched_at,
                    'lat' => (float)$row->lat,
                    'lon' => (float)$row->lon,
                    'velocity' => (float)$row->velocity,
                    'altitude' => (float)$row->altitude,
                ];
            }, array_reverse($rows)); // Reverse to get chronological order

            return response()->json(['points' => $points]);
        } catch (\Throwable $e) {
            return response()->json(['points' => [], 'error' => $e->getMessage()]);
        }
    }

    private function pipe(string $path)
    {
        $url = $this->base() . $path;
        try {
            $ctx = stream_context_create([
                'http' => ['timeout' => 5, 'ignore_errors' => true],
            ]);
            $body = @file_get_contents($url, false, $ctx);
            if ($body === false || trim($body) === '') {
                $body = '{}';
            }
            json_decode($body);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $body = '{}';
            }
            return new Response($body, 200, ['Content-Type' => 'application/json']);
        } catch (\Throwable $e) {
            return new Response('{"error":"upstream"}', 200, ['Content-Type' => 'application/json']);
        }
    }
}
