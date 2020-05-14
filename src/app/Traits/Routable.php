<?php


namespace Laurel\MultiRoute\App\Traits;

use Illuminate\Support\Facades\Route;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

/**
 * Trait for adding package routes
 *
 * Trait Routable
 * @package Laurel\MultiRoute\App\Traits
 */
trait Routable
{
    /**
     * Returns prefix
     *
     * @return null
     */
    public static function prefix()
    {
        return substr(request()->route()->getPrefix(), 0, 1) === "/" ? substr(request()->route()->getPrefix(), 1, strlen(request()->route()->getPrefix())) : request()->route()->getPrefix();
    }

    /**
     * Method registers routes
     *
     * @param null $prefix
     * @param array $methods
     */
    public static function routes($prefix = null, $methods = [])
    {
        if (empty($methods)) {
            $methods = config('multi-route.default_method');
        }

        if (!is_array($methods)) {
            $methods = [$methods];
        }
        foreach ($methods as $method) {
            self::checkRouteMethod($method);
            self::createRouteForMethod($prefix, $method);
        }
    }

    /**
     * Checks request method
     *
     * @param $method
     */
    public static function checkRouteMethod($method)
    {
        if (!in_array($method, config('multi-route.allowed_methods'))) {
            throw new MethodNotAllowedException(config('multi-route.allowed_methods'), "Method {$method} is not allowed.");
        }
    }

    /**
     * Creates route for method
     *
     * @param $method
     * @param null $prefix
     */
    public static function createRouteForMethod($prefix, $method)
    {
        Route::namespace('\Laurel\MultiRoute')->prefix($prefix ?? '')->group(function() use ($method) {
            Route::$method('{path?}', 'MultiRoute@handle')->where('path', '.*')->name('multi-route.index');
        });
    }
}
