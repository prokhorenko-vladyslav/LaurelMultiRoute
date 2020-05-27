<?php

use Laurel\MultiRoute\App\Models\Path;
use Laurel\MultiRoute\MultiRoute;

if (!function_exists('homepage')) {
    /**
     * Helper for loading homepage
     *
     * @return Path|null
     */
    function homepage(): ?Path
    {
        return MultiRoute::getHomepage();
    }
}

if (!function_exists('pathForSlug')) {
    /**
     * Helper for creating path chain using slug
     *
     * @param string $slug
     * @return array
     */
    function pathForSlug(string $slug): array
    {
        return MultiRoute::pathForSlug($slug);
    }
}

if (!function_exists('pathForId')) {
    /**
     * Helper for creating path chain using id
     *
     * @param int $id
     * @return array
     */
    function pathForId(int $id): array
    {
        return MultiRoute::pathForId($id);
    }
}

if (!function_exists('uriForSlug')) {
    /**
     * Helper for creating path uri using slug
     *
     * @param string $slug
     * @return string
     */
    function uriForSlug(string $slug): string
    {
        return MultiRoute::uriForSlug($slug);
    }
}

if (!function_exists('uriForId')) {
    /**
     * Helper for creating path uri using id
     *
     * @param int $id
     * @return string
     */
    function uriForId(int $id): string
    {
        return MultiRoute::uriForId($id);
    }
}

if (!function_exists('multiRoutes')) {
    /**
     * Generates routes
     *
     * @param null $prefix
     * @param array $methods
     */
    function multiRoutes($prefix = null, $methods = [])
    {
        MultiRoute::routes($prefix, $methods);
    }
}
