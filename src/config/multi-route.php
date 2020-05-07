<?php
    return [
        'default_method' => 'any',
        'allowed_methods' => [
            'any'
        ],

        'prepare_slug' => true,

        'process_404' => true,
        'not_found_controller' => "\App\Http\Controllers\NotFoundController@not_found",

        'use_cache' => false,
        'cache_storage' => env('CACHE_DRIVER'),
        'cache_lifetime' => 50,

        // Change or not children, when parent are deleting
        'set_null_on_delete' => true,

        'is_path_active_after_adding' => true,
    ];
