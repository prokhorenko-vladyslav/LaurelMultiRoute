<?php


namespace Laurel\MultiRoute\App\Traits;


use Laurel\MultiRoute\MultiRoute;

trait HasCache
{
    public function removeFromCache()
    {
        if (config('multi-route.use_cache', false)) {
            $uri = MultiRoute::pathForId($this->id);
            MultiRoute::removeFromCache($uri);
        }
    }

    public function saveToCache()
    {
        if (config('multi-route.use_cache', false)) {
            $uri = MultiRoute::pathForId($this->id);
            MultiRoute::saveToCache($this->callback, $uri);
        }
    }
}
