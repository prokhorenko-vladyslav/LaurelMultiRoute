<?php


namespace Laurel\MultiRoute\App\Traits;


trait Sluggable
{
    public static function getBySlug($slug) : ?self
    {
        return self::where('slug', $slug)->first();
    }

    public static function getBySlugOrFail($slug) : ?self
    {
        return self::where('slug', $slug)->firstOrFail();
    }

    public function setSlugAttribute(string $slug)
    {
        $this->attributes["slug"] = $slug;
    }
}
