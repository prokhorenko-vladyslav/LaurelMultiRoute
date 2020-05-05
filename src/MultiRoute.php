<?php


namespace Laurel\MultiRoute;

use Illuminate\Support\Facades\Route;
use Laurel\MultiRoute\App\Models\Path;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class MultiRoute
{
    public static function handle()
    {
        return app()->call("App\Http\Controllers\TestController@index");
    }

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

    public static function checkRouteMethod($method)
    {
        if (!in_array($method, config('multi-route.allowed_methods'))) {
            throw new MethodNotAllowedException(config('multi-route.allowed_methods'), "Method {$method} is not allowed.");
        }
    }

    public static function createRouteForMethod($method)
    {
        Route::namespace('\Laurel\MultiRoute')->group(function() use ($method) {
            Route::$method('{path?}', 'MultiRoute@handle')->where('path', '.*')->name('multi-route.index');
        });
    }

    public static function path()
    {

    }

    public static function isParent()
    {

    }

    public static function isChild()
    {

    }

    public static function isParentRecursive()
    {

    }

    public static function isChildRecursive()
    {

    }

    public static function addPath(string $slug, string $callback, Path $parent = null)
    {
        self::checkCallback($callback);
        $path = new Path([
            'slug' => $slug,
            'callback' => $callback
        ]);
        ddd($slug, $callback, $parent);
    }

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

    private static function throwIncorrectCallbackException()
    {
        throw new \Exception('Path callback is incorrect');
    }
}
