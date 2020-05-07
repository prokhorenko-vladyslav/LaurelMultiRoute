<?php


namespace Laurel\MultiRoute\App\Traits;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laurel\MultiRoute\app\Exceptions\RouteRecursionException;

trait Parentable
{
    public function setParentIdAttribute(?int $parentId)
    {
        $this->checkRecursiveForId($parentId);
        $this->attributes["parent_id"] = $parentId;
    }

    public function parent() : BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children() : HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function checkRecursiveForId(?int $parentId) : bool
    {
        if ($this->id === $parentId) {
            throw new RouteRecursionException("Element can not for self parent");
        }
        return $this->checkRecursive($this, $parentId);
    }

    public function checkRecursive(self $item, ?int $searchId)
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

    protected function changeChildrenParent()
    {
        if (config('multi-route.set_null_on_delete', false)) {
            foreach ($this->load('children')->children as $child) {
                $child->parent_id = null;
                $child->save();
            }
        }
    }

}
