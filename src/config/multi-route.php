<?php
    return [
        'default_method' => 'any',
        'allowed_methods' => [
            'any'
        ],

        'prepare_slug' => true,

        'process_404' => true,
        'not_found_controller' => "\App\Http\Controllers\NotFoundController@not_found",

        'use_cache' => true,
        'cache_storage' => env('CACHE_DRIVER'),
        'cache_lifetime' => 50
    ];
