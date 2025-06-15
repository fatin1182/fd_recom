<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SpoonacularService
{
    protected array $apiKeys;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKeys = config('services.spoonacular.api_keys');
        $this->baseUrl = config('services.spoonacular.base_url');
    }

    public function getRecipes(array $params = [])
    {
        $endpoint = '/recipes/complexSearch';

        foreach ($this->apiKeys as $apiKey) {
            $params['apiKey'] = $apiKey;

            $cacheKey = $this->makeCacheKey($endpoint, $params);

            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $response = Http::get($this->baseUrl . $endpoint, $params);

            if ($response->successful()) {
                $data = $response->json();
                Cache::put($cacheKey, $data, now()->addMinutes(30));
                return $data;
            }

            // Handle quota exceeded or bad key
            if ($response->status() === 402 || $response->status() === 429) {
                continue; // try next key
            }
        }

        throw new \Exception('All API keys have failed or exceeded quota.');
    }

    protected function makeCacheKey(string $endpoint, array $params): string
    {
        // Remove apiKey from cache key for clarity, or include it if needed
        $keyParams = array_diff_key($params, ['apiKey' => '']);
        return 'spoonacular_' . md5($endpoint . json_encode($keyParams));
    }
}
