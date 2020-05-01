<?php


namespace Laurel\MultiRoute;


use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

class MultiRoute
{
    public static function handle()
    {
        //app()->call("App\Http\Controllers\TestController@index");
    }

    public static function routes()
    {
        Route::namespace('\Laurel\MultiRoute')->group(function() {
           Route::any('{path?}', 'MultiRoute@handle')->where('path', '.*')->name('multi-route.index');
        });
    }

    public static function path()
    {

    }

    public static function isParent()
    {

    }

    public static function isChild()
    {

    }

    public static function isParentRecursive()
    {

    }

    public static function isChildRecursive()
    {

    }
}
