<?php


namespace Laurel\MultiRoute\App\Traits;


use Laurel\MultiRoute\App\Models\Path;

trait Pathable
{
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

    public static function pathForSlug(string $slug)
    {
        $pathChain = self::buildPathChainForSlug($slug);
        return self::composePath($pathChain);
    }

    public static function pathForId(int $id)
    {
        $pathChain = self::buildPathChainForId($id);
        return self::composePath($pathChain);
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

    public static function checkSlugUnique(string $slug, Path $parent = null)
    {
        $exists = Path::where('slug', $slug)->where('parent_id', $parent->id ?? null)->exists();
        if ($exists) {
            self::throwPathAlreadyExistsException($slug);
        }

        return true;
    }

    public static function getHomepage()
    {
        return Path::whereNull('slug')->first();
    }
}
