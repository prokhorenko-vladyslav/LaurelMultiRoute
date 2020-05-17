<?php


namespace Laurel\MultiRoute;

use Exception;
use Illuminate\Support\Facades\Log;
use Laurel\MultiRoute\App\Models\Path;
use Laurel\MultiRoute\App\Traits\Cachable;
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
    use Throwable, Cachable, Routable, Pathable;

    /**
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
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
            } catch (Exception $e) {
                Log::error($e->getMessage(), [ 'uri' => request()->getRequestUri() ]);
            }
        }

        if (!$callback || !$path) {
            [$callback, $path] = self::getPathAttributesFromDB();
        }

        $currentPath = $path[count($path) - 1];
        $result = self::callMiddleware($currentPath);
        if ($result !== true) {
            return $result;
        } else {
             return app()->call($callback, [
                'path' => $path
            ]);
        }
    }

    /**
     * Calls path middleware
     *
     * @param Path $path
     * @return bool
     */
    public static function callMiddleware(Path $path)
    {
        foreach ($path->middleware() as $middleware) {
            if (is_null($middleware)) {
                continue;
            }
            $result = app($middleware)->handle(request(), 1, '');

            if ($result !== true) {
                return $result;
            }
        }

        return true;
    }

    /**
     * @param string $callback
     * @return bool
     * @throws Exception
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
