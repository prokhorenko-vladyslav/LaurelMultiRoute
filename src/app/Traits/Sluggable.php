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
     * @param $slug
     * @return static|null
     */
    public static function getBySlug($slug) : ?self
    {
        return self::where('slug', $slug)->first();
    }

    /**
     * Gets path by slug or fail, if it has not been found
     *
     * @param $slug
     * @return static|null
     */
    public static function getBySlugOrFail($slug) : ?self
    {
        return self::where('slug', $slug)->firstOrFail();
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
