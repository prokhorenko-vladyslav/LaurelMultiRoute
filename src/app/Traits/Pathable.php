<?php


namespace Laurel\MultiRoute\App\Traits;


use Laurel\MultiRoute\App\Models\Path;

/**
 * Trait Pathable
 * @package Laurel\MultiRoute\App\Traits
 */
trait Pathable
{
    /**
     * @return array
     */
    public static function getPathAttributesFromDB()
    {
        $path = self::buildPathChain();
        if (empty($path)) {
            self::throw404Exception(request()->getRequestUri());
        }
        $callback = $path[count($path) - 1]->callback;
        self::checkCallback($callback);
        self::saveToCache($callback, $path);
        return [$callback, $path];
    }

    /**
     * @param string $slug
     * @return mixed
     */
    public static function pathForSlug(string $slug)
    {
        $pathChain = self::buildPathChainForSlug($slug);
        return self::composePath($pathChain);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public static function pathForId(int $id)
    {
        $pathChain = self::buildPathChainForId($id);
        return self::composePath($pathChain);
    }

    /**
     * @param string $slug
     * @param string $callback
     * @param Path|null $parent
     * @return bool
     */
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

    /**
     * @param string $slug
     * @param Path|null $parent
     * @return bool
     */
    public static function checkSlugUnique(string $slug, Path $parent = null)
    {
        $exists = Path::where('slug', $slug)->where('parent_id', $parent->id ?? null)->exists();
        if ($exists) {
            self::throwPathAlreadyExistsException($slug);
        }

        return true;
    }

    /**
     * @return mixed
     */
    public static function getHomepage()
    {
        return Path::whereNull('slug')->first();
    }
}
