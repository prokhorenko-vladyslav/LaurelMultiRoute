<?php


namespace Laurel\MultiRoute\App\Traits;


/**
 * Trait for manipulating path slug
 *
 * Trait Sluggable
 * @package Laurel\MultiRoute\App\Traits
 */
trait Sluggable
{
    /**
     * Gets path by slug
     *
     * @param string $slug
     * @param string|null $prefix
     * @return static|null
     */
    public static function getBySlug(string $slug, string $prefix = null) : ?self
    {
        $query = self::where('slug', $slug);
        return empty($prefix) ? $query->whereNull('prefix')->first() : $query->where('prefix', $prefix)->first();
    }

    /**
     * Gets path by slug or fail, if it has not been found
     *
     * @param string $slug
     * @param string|null $prefix
     * @return static|null
     */
    public static function getBySlugOrFail(string $slug, string $prefix = null) : ?self
    {
        $query = self::where('slug', $slug);
        return empty($prefix) ? $query->whereNull('prefix')->first() : $query->where('prefix', $prefix)->firstOrFail();
    }

    /**
     * Sets path slug
     *
     * @param string $slug
     */
    public function setSlugAttribute(string $slug)
    {
        $this->attributes["slug"] = $slug;
    }
}
