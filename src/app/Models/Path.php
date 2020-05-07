<?php

namespace Laurel\MultiRoute\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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

    public function setParentIdAttribute($parent_id)
    {
        dd($parent_id);
    }
}
