<?php

namespace Laurel\MultiRoute\App\Models;

use Illuminate\Database\Eloquent\Model;
use Laurel\MultiRoute\App\Traits\Activetable;
use Laurel\MultiRoute\App\Traits\CanBeHomepage;
use Laurel\MultiRoute\App\Traits\HasCache;
use Laurel\MultiRoute\App\Traits\Parentable;
use Laurel\MultiRoute\App\Traits\Sluggable;

class Path extends Model
{
    use Activetable, Parentable, HasCache, Sluggable, CanBeHomepage;

    protected $fillable = ['slug', 'callback'];

    public static function getById($id) : ?self
    {
        return self::find($id);
    }

    public static function getByIdOrFail($id) : ?self
    {
        return self::findOrFail($id);
    }

    public function save(array $options = [])
    {
        $this->saveToCache();
        return parent::save();
    }

    public function delete()
    {
        $this->changeChildrenParent();
        $this->removeFromCache();
        return parent::delete();
    }
}
