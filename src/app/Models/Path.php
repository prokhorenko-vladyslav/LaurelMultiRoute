<?php

namespace Laurel\MultiRoute\App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laurel\MultiRoute\App\Traits\Activetable;
use Laurel\MultiRoute\App\Traits\CanBeHomepage;
use Laurel\MultiRoute\App\Traits\HasCache;
use Laurel\MultiRoute\App\Traits\HasMiddleware;
use Laurel\MultiRoute\App\Traits\Parentable;
use Laurel\MultiRoute\App\Traits\Sluggable;

/**
 * Path model
 *
 * Class Path
 * @package Laurel\MultiRoute\App\Models
 */
class Path extends Model
{
    use Activetable, Parentable, HasCache, HasMiddleware, Sluggable, CanBeHomepage;

    /**
     * Fillable options
     *
     * @var string[]
     */
    protected $fillable = ['slug', 'callback', 'middleware_list'];

    /**
     * Cast options
     *
     * @var string[]
     */
    protected $casts = [
        "middleware" => "array"
    ];

    /**
     * Returns path by id or null
     *
     * @param int $id Path id
     * @return self|null
     */
    public static function getById($id) : ?self
    {
        return self::find($id);
    }

    /**
     * Returns path by id or throws ModelNotFoundException
     *
     * @param $id
     * @return self|null
     * @throws ModelNotFoundException
     */
    public static function getByIdOrFail($id) : ?self
    {
        return self::findOrFail($id);
    }

    /**
     * Overriding of save method of model. Now, it saves path in the cache storage
     *
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $this->saveToCache();
        return parent::save();
    }

    /**
     * Overriding of delete method of model.
     * Now, it delete path from the cache storage and set parent to null for all children
     *
     * @return bool|null
     * @throws Exception
     */
    public function delete()
    {
        $this->changeChildrenParent();
        $this->removeFromCache();
        return parent::delete();
    }
}
