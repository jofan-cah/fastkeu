<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BefastApiService
{
    protected $baseUrl;
    protected $token;
    protected $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.befast.base_url');
        $this->token = config('services.befast.api_token');
        $this->timeout = config('services.befast.timeout', 30);
    }

    /**
     * Make HTTP request to BEFAST API
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    protected function makeRequest($method, $endpoint, $data = [])
    {
        try {
            $url = $this->baseUrl . '/api/' . ltrim($endpoint, '/');

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept' => 'application/json',
                ])
                ->{$method}($url, $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            Log::warning('BEFAST API Error Response', [
                'status' => $response->status(),
                'body' => $response->body(),
                'endpoint' => $endpoint
            ]);

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'API request failed',
                'status_code' => $response->status(),
            ];

        } catch (\Exception $e) {
            Log::error('BEFAST API Exception', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Test API connection
     *
     * @return array
     */
    public function testConnection()
    {
        return $this->makeRequest('get', '/test');
    }

    // ============================================
    // SUBSCRIPTIONS
    // ============================================

    /**
     * Get all subscriptions
     *
     * @param array $params
     * @return array
     */
    public function getSubscriptions($params = [])
    {
        $queryString = http_build_query($params);
        return $this->makeRequest('get', "/subscriptions?{$queryString}");
    }

    /**
     * Get subscriptions for dropdown (with cache)
     *
     * @param array $params
     * @return array
     */
    public function getSubscriptionsDropdown($params = [])
    {
        $cacheKey = 'befast_subscriptions_dropdown_' . md5(json_encode($params));

        return Cache::remember($cacheKey, 300, function () use ($params) { // Cache 5 menit
            $queryString = http_build_query($params);
            return $this->makeRequest('get', "/subscriptions/dropdown?{$queryString}");
        });
    }

    /**
     * Get subscription by ID
     *
     * @param string $id
     * @return array
     */
    public function getSubscription($id)
    {
        return $this->makeRequest('get', "/subscriptions/{$id}");
    }

    // ============================================
    // PAKETS
    // ============================================

    /**
     * Get all pakets
     *
     * @param array $params
     * @return array
     */
    public function getPakets($params = [])
    {
        $queryString = http_build_query($params);
        return $this->makeRequest('get', "/pakets?{$queryString}");
    }

    /**
     * Get pakets for dropdown (with cache)
     *
     * @param array $params
     * @return array
     */
    public function getPaketsDropdown($params = [])
    {
        $cacheKey = 'befast_pakets_dropdown_' . md5(json_encode($params));

        return Cache::remember($cacheKey, 600, function () use ($params) { // Cache 10 menit
            $queryString = http_build_query($params);
            return $this->makeRequest('get', "/pakets/dropdown?{$queryString}");
        });
    }

    /**
     * Get paket by ID
     *
     * @param string $id
     * @return array
     */
    public function getPaket($id)
    {
        return $this->makeRequest('get', "/pakets/{$id}");
    }

    // ============================================
    // KARYAWAN
    // ============================================

    /**
     * Get all karyawan
     *
     * @param array $params
     * @return array
     */
    public function getKaryawan($params = [])
    {
        $queryString = http_build_query($params);
        return $this->makeRequest('get', "/karyawan?{$queryString}");
    }

    /**
     * Get karyawan for dropdown (with cache)
     *
     * @param array $params
     * @return array
     */
    public function getKaryawanDropdown($params = [])
    {
        $cacheKey = 'befast_karyawan_dropdown_' . md5(json_encode($params));

        return Cache::remember($cacheKey, 300, function () use ($params) { // Cache 5 menit
            $queryString = http_build_query($params);
            return $this->makeRequest('get', "/karyawan/dropdown?{$queryString}");
        });
    }

    /**
     * Get karyawan by ID
     *
     * @param string $id
     * @return array
     */
    public function getKaryawanById($id)
    {
        return $this->makeRequest('get', "/karyawan/{$id}");
    }

    // ============================================
    // CACHE MANAGEMENT
    // ============================================

    /**
     * Clear all BEFAST API cache
     *
     * @return void
     */
    public function clearCache()
    {
        Cache::flush();
        Log::info('BEFAST API cache cleared');
    }

    /**
     * Clear specific cache by pattern
     *
     * @param string $pattern
     * @return void
     */
    public function clearCacheByPattern($pattern)
    {
        $keys = Cache::get('befast_cache_keys', []);

        foreach ($keys as $key) {
            if (str_contains($key, $pattern)) {
                Cache::forget($key);
            }
        }

        Log::info('BEFAST API cache cleared for pattern: ' . $pattern);
    }
}
