<?php

declare(strict_types = 1);

use Yesccx\DBQuery\Contracts\ConnectionManagerContract;
use Yesccx\DBQuery\Contracts\QueriesContract;
use Yesccx\DBQuery\Foundation\ConnectionManager;
use Yesccx\DBQuery\Foundation\Queries;

return [
    /**
     * DBQuery cache
     */
    'cache' => [
        'enabled' => env('YDQ_CACHE_ENABLED', false),

        'driver' => env('YDQ_CACHE_DRIVER', env('CACHE_DRIVER')),
    ],

    'dependencies' => [
        ConnectionManagerContract::class => ConnectionManager::class,
        QueriesContract::class           => Queries::class,
    ],
];
