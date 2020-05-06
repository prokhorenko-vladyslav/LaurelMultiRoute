<?php


namespace Laurel\MultiRoute;

use Exception;
use Illuminate\Support\Facades\Route;
use Laurel\MultiRoute\App\Models\Path;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class MultiRoute
{
    public static function handle()
    {
        $path = self::buildPathChain();
        dd($path);
        return app()->call("App\Http\Controllers\TestController@index");
    }

    public static function buildPathChain(string $path = null)
    {
        $path = $path ?? request()->getRequestUri();
        $uriParts = self::explodeUri($path);
        return self::createPathChainFromUriParts($uriParts);
    }

    public static function createPathChainFromUriParts(array $uriParts)
    {
        $parent = null;
        $pathChain = [];
        foreach ($uriParts as $slug) {
            $path = Path::where('slug', $slug)->first();
            if (!$path) {
                self::throwPathNotFoundException($slug);
            }

            if ($path->parent_id !== $parent) {
                self::throwParentIsIncorrectException($path->id, $parent);
            }

            $parent = $path->id;
            $pathChain[] = $path;
        }

        return $pathChain;
    }

    public static function throwPathNotFoundException(string $slug)
    {
        throw new Exception("Path with slug `{$slug}` has not been found");
    }

    public static function throwParentIsIncorrectException($childId, $parentId)
    {
        throw new Exception("Path with id `{$childId}` is not child of item with id `{$parentId}`");
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

    public static function path(string $path = null)
    {

    }

    public static function explodeUri(string $path)
    {
        $pathWithoutGetParams = explode("?", $path)[0];
        $pathParts = explode("/", $pathWithoutGetParams);
        return self::clearPathParts($pathParts);
    }

    public static function clearPathParts(array $pathParts)
    {
        $countOfParts = count($pathParts);
        for ($i = 0; $i < $countOfParts; $i++) {
            $pathParts[$i] = trim($pathParts[$i]);
            if (!strlen($pathParts[$i])) {
                unset($pathParts[$i]);
            }
        }

        return array_values($pathParts);
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
        self::checkSlugUnique($slug, $parent);
        $path = new Path([
            'slug' => $slug,
            'callback' => $callback
        ]);

        if (!is_null($parent)) {
            $path->parent()->associate($parent);
        }

        return $path->save();
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
        throw new Exception('Path callback is incorrect');
    }

    public static function checkSlugUnique(string $slug, Path $parent = null)
    {
        $exists = Path::where('slug', $slug)->where('parent_id', $parent->id ?? null)->exists();
        if ($exists) {
            self::throwPathAlreadyExistsException($slug);
        }

        return true;
    }

    private static function throwPathAlreadyExistsException(string $slug)
    {
        throw new Exception("Path with slug `{$slug}` already exists");
    }
}
