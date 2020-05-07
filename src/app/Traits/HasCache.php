<?php


namespace Laurel\MultiRoute\App\Traits;


use Laurel\MultiRoute\MultiRoute;

trait HasCache
{
    public function removeFromCache()
    {
        if (config('multi-route.use_cache', false)) {
            $uri = MultiRoute::uriForId($this->id);
            MultiRoute::removeFromCache($uri);
        }
    }

    public function saveToCache()
    {
        if (config('multi-route.use_cache', false)) {
            $uri = MultiRoute::uriForId($this->id);
            $path = MultiRoute::pathForId($this->id);
            MultiRoute::saveToCache($uri, $this->callback, $path);
        }
    }
}
