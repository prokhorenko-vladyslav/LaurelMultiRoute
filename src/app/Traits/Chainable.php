<?php


namespace Laurel\MultiRoute\App\Traits;


use Laurel\MultiRoute\App\Models\Path;

/**
 * Class Chainable
 * @package Laurel\MultiRoute\App\Traits
 */
trait Chainable
{
    /**
     * @param string|null $path
     * @return array
     */
    public static function buildPathChain(string $path = null)
    {
        $path = $path ?? request()->getRequestUri();
        $uriParts = self::explodeUri($path);
        return self::createPathChainFromUriParts($uriParts);
    }

    /**
     * @param array $uriParts
     * @return array
     */
    public static function createPathChainFromUriParts(array $uriParts)
    {
        $parent = null;
        $pathChain = [];
        foreach ($uriParts as $slug) {
            $slug = self::prepareSlug($slug);
            $path = Path::getBySlug($slug);;
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
     * @param string $slug
     * @return array
     */
    public static function buildPathChainForSlug(string $slug)
    {
        $pathChain = [];
        do {
            $path = Path::getBySlug($slug);
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
     * @param int $id
     * @return array
     */
    public static function buildPathChainForId(int $id)
    {
        $pathChain = [];
        do {
            $path = Path::find($id);
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
     * @param array $pathChain
     * @return string
     */
    public static function composePath(array $pathChain)
    {
        $uri = "";
        foreach ($pathChain as $path) {
            $uri .= "/{$path->slug}";
        }
        return $uri;
    }

    /**
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
