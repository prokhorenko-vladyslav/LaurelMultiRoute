<?php


namespace Laurel\MultiRoute\App\Traits;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laurel\MultiRoute\app\Exceptions\RouteRecursionException;

/**
 * Trait for creating child-parent relationships
 *
 * Trait Parentable
 * @package Laurel\MultiRoute\App\Traits
 */
trait Parentable
{
    use Throwable;

    /**
     * Checks for recursion and sets parent_id, if all is ok
     *
     * @param int|null $parentId
     * @throws RouteRecursionException
     */
    public function setParentIdAttribute(?int $parentId)
    {
        $this->checkRecursionForId($parentId);
        if ($this->getPrefix() !== self::getByIdOrFail($parentId)->getPrefix()) {
            self::throwIncorrectPrefixException();
        }

        $this->attributes["parent_id"] = $parentId;
    }

    /**
     * Method for creating child-parent relationships
     *
     * @return BelongsTo
     */
    public function parent() : BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Method for creating parent-child relationships
     *
     * @return HasMany
     */
    public function children() : HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Checks for recursion
     *
     * @param int|null $parentId
     * @return bool
     * @throws RouteRecursionException
     */
    public function checkRecursionForId(?int $parentId) : bool
    {
        if ($this->id === $parentId) {
            throw new RouteRecursionException("Element can not be self parent");
        }
        return $this->checkRecursion($this, $parentId);
    }

    /**
     * Gets all item children and checks it for equality to @searchId
     *
     * @param Parentable $item
     * @param int|null $searchId
     * @return bool
     * @throws RouteRecursionException
     */
    public function checkRecursion(self $item, ?int $searchId)
    {
        if (is_null($searchId)) {
            foreach ($item->load('children')->children as $child) {
                if ($child->id === $searchId) {
                    throw new RouteRecursionException("Founded recursive for {$child->slug}[{$child->id}] and {$this->slug}[{$this->id}]");
                }

                if ($child->children()->count()) {
                    $this->checkRecursion($child, $searchId);
                }
            }
        }

        return true;
    }

    /**
     * If parameter "Set null on delete" is true, method gets all children and removes parent-child relationship
     *
     * @return void
     */
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
