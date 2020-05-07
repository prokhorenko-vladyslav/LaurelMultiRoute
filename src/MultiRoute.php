<?php


namespace Laurel\MultiRoute;

use Illuminate\Support\Facades\Log;
use Laurel\MultiRoute\App\Traits\Cachable;
use Laurel\MultiRoute\App\Traits\Chainable;
use Laurel\MultiRoute\App\Traits\Pathable;
use Laurel\MultiRoute\App\Traits\Routable;
use Laurel\MultiRoute\App\Traits\Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class MultiRoute
 * @package Laurel\MultiRoute
 */
class MultiRoute
{
    use Throwable, Cachable, Routable, Chainable, Pathable;

    /**
     * @return mixed
     */
    public static function handle()
    {
        if (config('multi-route.process_404')) {
            try {
                return self::processRequest();
            } catch (NotFoundHttpException $e) {
                return app()->call(config('multi-route.not_found_controller'));
            }
        } else {
            return self::processRequest();
        }
    }

    /**
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function processRequest()
    {
        $callback = false;
        $path = false;
        if (self::getCacheStorage()->has(request()->getRequestUri()) && config('multi-route.use_cache')) {
            try {
                [$callback, $path] = self::getPathAttributesFromCache();
            } catch (\Exception $e) {
                Log::error($e->getMessage(), [ 'uri' => request()->getRequestUri() ]);
            }
        }

        if (!$callback || !$path) {
            [$callback, $path] = self::getPathAttributesFromDB();
        }

        return app()->call($callback, [
            'path' => $path
        ]);
    }

    /**
     * @param string $callback
     * @return bool
     */
    public static function checkCallback(string $callback)
    {
        $parts = explode("@", $callback);
        if (count($parts) !== 2) {
            self::throwIncorrectCallbackException();
        }
        $controller = $parts[0];
        $method = $parts[1];

        if (!class_exists($controller) || !method_exists($controller, $method)) {
            self::throwIncorrectCallbackException();
        }

        return true;
    }
}
