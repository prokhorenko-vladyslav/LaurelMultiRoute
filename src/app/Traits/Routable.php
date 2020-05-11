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
     * Method registers routes
     *
     * @param array $methods
     */
    public static function routes($methods = [])
    {
        if (empty($methods)) {
            $methods = config('multi-route.default_method');
        }

        if (!is_array($methods)) {
            $methods = [$methods];
        }
        foreach ($methods as $method) {
            self::checkRouteMethod($method);
            self::createRouteForMethod($method);
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
     */
    public static function createRouteForMethod($method)
    {
        Route::namespace('\Laurel\MultiRoute')->group(function() use ($method) {
            Route::$method('{path?}', 'MultiRoute@handle')->where('path', '.*')->name('multi-route.index');
        });
    }
}
