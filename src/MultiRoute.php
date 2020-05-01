<?php


namespace Laurel\MultiRoute;


use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class MultiRoute
{
    public static function handle()
    {
        app()->call("App\Http\Controllers\TestController@index");
    }

    public static function routes($methods = [])
    {
        if (empty($methods)) {
            self::createRouteForMethod('any');
            return;
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
        if (!in_array($method, config('multi-route.allowed_methods')))
            throw new MethodNotAllowedException("Method {$method} is not allowed.");
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
}
