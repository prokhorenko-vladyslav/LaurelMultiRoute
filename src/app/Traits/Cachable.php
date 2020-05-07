<?php


namespace Laurel\MultiRoute\App\Traits;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;

/**
 * Trait Cachable
 * @package Laurel\MultiRoute\App\Traits
 */
trait Cachable
{
    /**
     * @return Repository
     */
    protected static function getCacheStorage() : Repository
    {
        return Cache::store(config('multi-route.cache_storage', env('CACHE_DRIVER')));
    }

    /**
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function getPathAttributesFromCache()
    {
        $attributes = self::getCacheStorage()->get(request()->getRequestUri());
        return [$attributes['callback'], $attributes['path']];
    }

    /**
     * @param string $uri
     * @param string $callback
     * @param $path
     */
    public static function saveToCache(string $uri, string $callback, $path)
    {
        try {
            if (config('multi-route.use_cache', false)) {
                self::getCacheStorage()->put($uri, [
                    'path' => $path,
                    'callback' => $callback,
                ], now()->addMinutes(
                    floatval(config('multi-route.cache_lifetime', 10)))
                );
            }
        } catch (\Exception $e) {
            Log::error("Path has not been cached. " . $e->getMessage(), [
                'callback' => $callback,
                'path' => $path
            ]);
        }
    }

    /**
     * @param string $cacheKey
     */
    public static function removeFromCache(string $uri)
    {
        try {
            if (config('multi-route.use_cache', false)) {
                self::getCacheStorage()->pull($uri);
            }
        } catch (\Exception $e) {
            Log::error("Path `{$uri}` has not been deleted from cache. " . $e->getMessage());
        }
    }
}
