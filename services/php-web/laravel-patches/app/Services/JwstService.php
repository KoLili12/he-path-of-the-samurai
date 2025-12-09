<?php

namespace App\Services;

use App\Support\JwstHelper;
use Illuminate\Support\Facades\Cache;

class JwstService
{
    private JwstHelper $jwstHelper;
    private int $cacheTtl = 300; // 5 minutes

    public function __construct(JwstHelper $jwstHelper = null)
    {
        $this->jwstHelper = $jwstHelper ?? new JwstHelper();
    }

    /**
     * Get JWST feed with caching
     *
     * @param string $source
     * @param string $suffix
     * @param string $program
     * @param string $instrument
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getFeed(
        string $source = 'jpg',
        string $suffix = '',
        string $program = '',
        string $instrument = '',
        int $page = 1,
        int $perPage = 24
    ): array {
        $cacheKey = $this->buildCacheKey($source, $suffix, $program, $instrument, $page, $perPage);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($source, $suffix, $program, $instrument, $page, $perPage) {
            return $this->fetchFeed($source, $suffix, $program, $instrument, $page, $perPage);
        });
    }

    /**
     * Fetch JWST feed from API
     *
     * @param string $source
     * @param string $suffix
     * @param string $program
     * @param string $instrument
     * @param int $page
     * @param int $perPage
     * @return array
     */
    private function fetchFeed(
        string $source,
        string $suffix,
        string $program,
        string $instrument,
        int $page,
        int $perPage
    ): array {
        // Determine endpoint path
        $path = $this->buildPath($source, $suffix, $program);

        // Fetch data from JWST API
        $resp = $this->jwstHelper->get($path, ['page' => $page, 'perPage' => $perPage]);
        $list = $resp['body'] ?? ($resp['data'] ?? (is_array($resp) ? $resp : []));

        // Process items
        $items = $this->processItems($list, $instrument, $perPage);

        return [
            'source' => $path,
            'count' => count($items),
            'items' => $items,
        ];
    }

    /**
     * Build endpoint path based on source
     *
     * @param string $source
     * @param string $suffix
     * @param string $program
     * @return string
     */
    private function buildPath(string $source, string $suffix, string $program): string
    {
        if ($source === 'suffix' && $suffix !== '') {
            return 'all/suffix/' . ltrim($suffix, '/');
        }

        if ($source === 'program' && $program !== '') {
            return 'program/id/' . rawurlencode($program);
        }

        return 'all/type/jpg';
    }

    /**
     * Process items from API response
     *
     * @param array $list
     * @param string $instrumentFilter
     * @param int $perPage
     * @return array
     */
    private function processItems(array $list, string $instrumentFilter, int $perPage): array
    {
        $items = [];

        foreach ($list as $it) {
            if (!is_array($it)) {
                continue;
            }

            // Extract image URL
            $url = $this->extractImageUrl($it);
            if (!$url) {
                continue;
            }

            // Extract instruments
            $instList = $this->extractInstruments($it);

            // Apply instrument filter
            if ($instrumentFilter && $instList && !in_array($instrumentFilter, $instList, true)) {
                continue;
            }

            // Build item
            $items[] = $this->buildItem($it, $url, $instList);

            if (count($items) >= $perPage) {
                break;
            }
        }

        return $items;
    }

    /**
     * Extract image URL from item
     *
     * @param array $it
     * @return string|null
     */
    private function extractImageUrl(array $it): ?string
    {
        $loc = $it['location'] ?? $it['url'] ?? null;
        $thumb = $it['thumbnail'] ?? null;

        foreach ([$loc, $thumb] as $u) {
            if (is_string($u) && preg_match('~\.(jpg|jpeg|png)(\?.*)?$~i', $u)) {
                return $u;
            }
        }

        return JwstHelper::pickImageUrl($it);
    }

    /**
     * Extract instruments from item
     *
     * @param array $it
     * @return array
     */
    private function extractInstruments(array $it): array
    {
        $instList = [];

        foreach (($it['details']['instruments'] ?? []) as $I) {
            if (is_array($I) && !empty($I['instrument'])) {
                $instList[] = strtoupper($I['instrument']);
            }
        }

        return $instList;
    }

    /**
     * Build item for response
     *
     * @param array $it
     * @param string $url
     * @param array $instList
     * @return array
     */
    private function buildItem(array $it, string $url, array $instList): array
    {
        $loc = $it['location'] ?? $it['url'] ?? null;

        return [
            'url' => $url,
            'obs' => (string)($it['observation_id'] ?? $it['observationId'] ?? ''),
            'program' => (string)($it['program'] ?? ''),
            'suffix' => (string)($it['details']['suffix'] ?? $it['suffix'] ?? ''),
            'inst' => $instList,
            'caption' => $this->buildCaption($it, $instList),
            'link' => $loc ?: $url,
        ];
    }

    /**
     * Build caption for item
     *
     * @param array $it
     * @param array $instList
     * @return string
     */
    private function buildCaption(array $it, array $instList): string
    {
        return trim(
            (($it['observation_id'] ?? '') ?: ($it['id'] ?? '')) .
            ' · P' . ($it['program'] ?? '-') .
            (($it['details']['suffix'] ?? '') ? ' · ' . $it['details']['suffix'] : '') .
            ($instList ? ' · ' . implode('/', $instList) : '')
        );
    }

    /**
     * Build cache key
     *
     * @param string $source
     * @param string $suffix
     * @param string $program
     * @param string $instrument
     * @param int $page
     * @param int $perPage
     * @return string
     */
    private function buildCacheKey(
        string $source,
        string $suffix,
        string $program,
        string $instrument,
        int $page,
        int $perPage
    ): string {
        return sprintf(
            'jwst:feed:%s:%s:%s:%s:%d:%d',
            $source,
            md5($suffix),
            md5($program),
            $instrument,
            $page,
            $perPage
        );
    }
}
