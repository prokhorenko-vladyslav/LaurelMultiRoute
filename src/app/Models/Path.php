<?php

namespace Laurel\MultiRoute\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laurel\MultiRoute\app\Exceptions\RouteRecursionException;
use Laurel\MultiRoute\MultiRoute;

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

    public static function getBySlugOrFail($slug) : ?self
    {
        return Path::where('slug', $slug)->firstOrFail();
    }

    public static function getById($id) : ?self
    {
        return Path::find($id);
    }

    public static function getByIdOrFail($id) : ?self
    {
        return Path::findOrFail($id);
    }

    public function setParentIdAttribute(?int $parentId)
    {
        $this->checkRecursiveForId($parentId);
        $this->attributes["parent_id"] = $parentId;
    }

    public function setSlugAttribute(string $slug)
    {
        $this->attributes["slug"] = $slug;
    }

    public function activate()
    {
        $this->is_active = true;
        $this->save();
    }

    public function deactivate()
    {
        $this->is_active = false;
        $this->save();
    }

    public function setIsActiveAttribute(bool $status)
    {
        $this->attributes['is_active'] = $status;
    }

    public function checkRecursiveForId(?int $parentId) : bool
    {
        if ($this->id === $parentId) {
            throw new RouteRecursionException("Element can not for self parent");
        }
        return $this->checkRecursive($this, $parentId);
    }

    public function checkRecursive(Path $item, ?int $searchId)
    {
        if (is_null($searchId)) {
            foreach ($item->load('children')->children as $child) {
                if ($child->id === $searchId) {
                    throw new RouteRecursionException("Founded recursive for {$child->slug}[{$child->id}] and {$this->slug}[{$this->id}]");
                }

                if ($child->children()->count()) {
                    $this->checkRecursive($child, $searchId);
                }
            }
        }

        return true;
    }

    public function delete()
    {
        $this->changeChildrenParent();
        $this->removeItemFromCache();
        return parent::delete();
    }

    protected function changeChildrenParent()
    {
        if (config('multi-route.set_null_on_delete', false)) {
            foreach ($this->load('children')->children as $child) {
                $child->parent_id = null;
                $child->save();
            }
        }
    }

    public function removeItemFromCache()
    {
        if (config('multi-route.use_cache', false)) {
            $uri = MultiRoute::pathForId($this->id);
            MultiRoute::removeFromCache($uri);
        }
    }

    public function isHomepage()
    {
        return is_null($this->slug);
    }

    public function makeHomepage(bool $deleteOld = false)
    {
        if ($this->isHomepage()) {
            return true;
        }

        if ($this->forgetHomepage($deleteOld)) {
            $this->attributes["slug"] = null;
            $this->attributes["parent_id"] = null;
            $this->save();
            return true;
        }

        return false;
    }

    public function forgetHomepage(bool $deleteOld = false)
    {
        $homepagePath = MultiRoute::getHomepage();
        if (!$homepagePath) {
            return true;
        }

        if ($deleteOld) {
            return $homepagePath->delete();
        } else {
            $homepagePath->slug = 'homepage-deleted-at-' . time();
            return $homepagePath->save();
        }
    }
}
