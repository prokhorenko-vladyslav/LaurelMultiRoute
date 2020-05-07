<?php

namespace Laurel\MultiRoute\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laurel\MultiRoute\app\Exceptions\RouteRecursionException;

class Path extends Model
{
    protected $fillable = ['slug', 'callback'];

    public function parent() : BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children() : HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function routable() : MorphTo
    {
        return $this->morphTo();
    }

    public static function getBySlug($slug) : ?self
    {
        return Path::where('slug', $slug)->first();
    }

    public static function getById($id) : ?self
    {
        return Path::find($id);
    }

    public function setParentIdAttribute(int $parentId)
    {
        $this->checkRecursiveForId($parentId);
        $this->attributes["parent_id"] = $parentId;
    }

    public function checkRecursiveForId(int $parentId) : bool
    {
        return $this->checkRecursive($this, $parentId);
    }

    public function checkRecursive(Path $item, int $searchId)
    {
        foreach ($item->load('children')->children as $child) {
            if ($child->id === $searchId) {
                throw new RouteRecursionException("Founded recursive for {$child->slug}[{$child->id}] and {$this->slug}[{$this->id}]");
            }

            if ($child->children()->count()) {
                $this->checkRecursive($child, $searchId);
            }
        }

        return true;
    }
}
