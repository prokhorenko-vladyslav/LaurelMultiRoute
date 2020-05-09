<?php


namespace Laurel\MultiRoute\App\Traits;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Trait for manipulating paths caching
 *
 * Trait Cachable
 * @package Laurel\MultiRoute\App\Traits
 */
trait Cachable
{
    /**
     * Return cache storage with package tags
     *
     * @return Repository
     */
    protected static function getCacheStorage() : Repository
    {
        return Cache::store(config('multi-route.cache_storage', env('CACHE_DRIVER')))->tags([ config('multi-route.cache_prefix', '') ]);
    }

    /**
     * Returns cached path
     *
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function getPathAttributesFromCache()
    {
        $attributes = self::getCacheStorage()->get(request()->getRequestUri());
        return [$attributes['callback'], $attributes['path']];
    }

    /**
     * Saves path to the cache
     *
     * @param string $uri
     * @param string $callback
     * @param $path
     *
     * @return void
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
     * Removes path from the cache
     *
     * @param string $uri
     * @return void
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

    /**
     * Delete all paths from the cache storage
     *
     * @return bool
     */
    public static function clearCache()
    {
        return (bool)self::getCacheStorage()->flush();
    }
}
