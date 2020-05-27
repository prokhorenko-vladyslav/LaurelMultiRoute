<?php


namespace Laurel\MultiRoute\App\Traits;


use Laurel\MultiRoute\App\Models\Path;

/**
 * Trait for manipulating paths
 *
 * Trait Pathable
 * @package Laurel\MultiRoute\App\Traits
 */
trait Pathable
{
    /**
     * Gets path from database, checks its status and callback. If cache is active, path data will save to cache storage
     *
     * @return array
     */
    public static function getPathAttributesFromDB()
    {
        [$currentPath, $callback, $path] = self::findSimplePath();
        if (!$currentPath) {
            [$currentPath, $callback, $path] = self::findComposedPath();
        }

        if ($currentPath->deactivated()) {
            self::throwPathNotActiveException($currentPath->slug);
        }

        self::checkCallback($callback);
        self::saveToCache(request()->getRequestUri(), $callback, $path);
        return [$callback, $path];
    }

    /**
     * Returns attributes of simple path, if its path attribute equals path parameter in the route
     *
     * @return array
     */
    public static function findSimplePath()
    {
        $uri = request()->route('path');
        $pathQuery = empty(self::prefix()) ? config('multi-route.path_model')::whereNull('prefix') : config('multi-route.path_model')::where('prefix', self::prefix());
        $currentPath = $pathQuery->where('path', $uri)->orWhere('path', "/{$uri}")->first();
        if (empty($currentPath)) {
            self::throw404Exception(request()->getRequestUri());
        }
        return [$currentPath, $currentPath->callback, [$currentPath]];
    }

    /**
     * Returns attributes of composed path, which has been found using parent-child relations
     *
     * @return array
     */
    public static function findComposedPath() : array
    {
        $path = self::buildPathChain();
        if (empty($path)) {
            self::throw404Exception(request()->getRequestUri());
        }
        $currentPath = $path[count($path) - 1];
        $callback = $currentPath->callback;

        return [$currentPath, $callback, $path];
    }

    /**
     * Gets path from database and creates uri for it using slug
     *
     * @param string $slug
     * @return mixed
     */
    public static function uriForSlug(string $slug)
    {
        $pathChain = self::pathForSlug($slug);
        return self::composePath($pathChain);
    }

    /**
     * Gets path from database and creates uri for it using id
     *
     * @param int $id
     * @return mixed
     */
    public static function uriForId(int $id)
    {
        $pathChain = self::pathForId($id);
        return self::composePath($pathChain);
    }

    /**
     * Creates new path and saves it to database
     *
     * @param string $slug
     * @param string $callback
     * @param Path|null $parent
     * @return bool
     */
    public static function addPath(string $slug, string $callback, Path $parent = null)
    {
        self::checkCallback($callback);
        self::checkSlugUnique($slug, $parent);
        $model = config('multi-route.path_model');
        $path = new $model([
            'slug' => $slug,
            'callback' => $callback
        ]);

        if (!is_null($parent)) {
            $path->parent()->associate($parent);
        }

        return $path->save();
    }

    /**
     * Checks if slug is unique
     *
     * @param string $slug
     * @param Path|null $parent
     * @return bool
     */
    public static function checkSlugUnique(string $slug, Path $parent = null)
    {
        $exists = config('multi-route.path_model')::where('slug', $slug)->where('parent_id', $parent->id ?? null)->exists();
        if ($exists) {
            self::throwPathAlreadyExistsException($slug);
        }

        return true;
    }

    /**
     * Returns homepage path
     *
     * @return mixed
     */
    public static function getHomepage()
    {
        return config('multi-route.path_model')::whereNull('slug')->first();
    }

    /**
     * Returns path chain
     *
     * @param string|null $path
     * @return array
     */
    public static function buildPathChain(string $path = null)
    {
        $path = $path ?? request()->route('path');
        $path = is_null($path) ? '' : $path;
        $uriParts = self::explodeUri($path);
        return self::createPathChainFromUriParts($uriParts);
    }

    /**
     * Creates path chain
     *
     * @param array $uriParts
     * @return array
     */
    public static function createPathChainFromUriParts(array $uriParts)
    {
        $parent = null;
        $pathChain = [];
        foreach ($uriParts as $slug) {
            $slug = self::prepareSlug($slug);
            $path = config('multi-route.path_model')::getBySlug($slug, self::prefix());
            if (!$path) {
                self::throw404Exception($slug);
            }

            if ($path->parent_id !== $parent) {
                self::throw404Exception("Path with id `{$path->id}` is not child of item with id `{$parent}`");
            }

            $parent = $path->id;
            $pathChain[] = $path;
        }

        if (!$pathChain && $homepage = self::getHomepage()) {
            $pathChain[] = $homepage;
        }

        return $pathChain;
    }

    /**
     * Creates parent chain for path using slug
     *
     * @param string $slug
     * @return array
     */
    public static function pathForSlug(string $slug)
    {
        $pathChain = [];
        do {
            $path = config('multi-route.path_model')::getBySlug($slug);
            if (!$path) {
                self::throwPathNotFoundException($slug);
            }

            $pathChain[] = $path;
            $parent = $path->load('parent')->parent;
            if ($parent) {
                $slug = $parent->slug;
            }
        } while ($parent !== null);

        return array_reverse($pathChain);
    }

    /**
     * Creates parent chain for path using id
     *
     * @param int $id
     * @return array
     */
    public static function pathForId(int $id)
    {
        $pathChain = [];
        do {
            $path = config('multi-route.path_model')::find($id);
            if (!$path) {
                self::throwPathNotFoundException($id);
            }

            $pathChain[] = $path;
            $parent = $path->load('parent')->parent;
            if ($parent) {
                $id = $parent->id;
            }
        } while ($parent !== null);

        return array_reverse($pathChain);
    }

    /**
     * Composes path using path chain
     *
     * @param array $pathChain
     * @return string
     */
    public static function composePath(array $pathChain)
    {
        $uri = "";
        foreach ($pathChain as $path) {
            $uri .= empty($path->path) ? "/{$path->slug}" : "/{$path->path}";
        }
        return $uri;
    }

    /**
     * Explodes uri
     *
     * @param string $path
     * @return array
     */
    public static function explodeUri(string $path)
    {
        $pathWithoutGetParams = explode("?", $path)[0];
        $pathParts = explode("/", $pathWithoutGetParams);
        return self::clearPathParts($pathParts);
    }

    /**
     * Removes from path parts spaces
     *
     * @param array $pathParts
     * @return array
     */
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

    /**
     * Replaces %20 in the slug to space
     *
     * @param string $slug
     * @return string|string[]|null
     */
    public static function prepareSlug(string $slug)
    {
        if (config('multi-route.prepare_slug')) {
            $slug = str_replace("%20", " ", $slug);
            return preg_replace("/[\s]{2,}/", " ", $slug);
        } else {
            return $slug;
        }
    }
}
