<?php
    return [
        /**
         * Default method for routes, if another has not been setted
         */
        'default_method' => 'any',

        /**
         * Allowed method for route
         */
        'allowed_methods' => [
            'any'
        ],

        /**
         * Need to clear slug from spaces and special symbols
         */
        'prepare_slug' => true,

        /**
         * Need to throw 404 error, if route has not been found
         */
        'process_404' => true,

        /**
         * Class and method, which will be called, if route has not been found
         */
        'not_found_controller' => "\App\Http\Controllers\NotFoundController@not_found",

        /**
         * Save paths to cache or not
         */
        'use_cache' => true,

        /**
         * Cache storage for saving paths
         */
        'cache_storage' => env('CACHE_DRIVER'),

        /**
         * Cache lifetime in minutes
         */
        'cache_lifetime' => 50,

        /**
         * Cache prefix for package
         */
        'cache_prefix' => 'laurel/multi-route',

        /**
         * Need to change, when parent deletes
         */
        'set_null_on_delete' => true,
    ];
