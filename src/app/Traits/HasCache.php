<?php


namespace Laurel\MultiRoute\App\Traits;


use Laurel\MultiRoute\MultiRoute;

/**
 * Trait, which save or remove paths from the cache storage
 *
 * Trait HasCache
 * @package Laurel\MultiRoute\App\Traits
 */
trait HasCache
{
    /**
     * If cache is activated, method removes path from the cache storage
     *
     * @return void
     */
    public function removeFromCache()
    {
        if (config('multi-route.use_cache', false)) {
            $uri = MultiRoute::uriForId($this->id);
            MultiRoute::removeFromCache($uri);
        }
    }

    /**
     * If cache is activated, method stores path in the cache storage
     *
     * @return void
     */
    public function saveToCache()
    {
        if (config('multi-route.use_cache', false)) {
            $uri = MultiRoute::uriForId($this->id);
            $path = MultiRoute::pathForId($this->id);
            MultiRoute::saveToCache($uri, $this->callback, $path);
        }
    }
}
