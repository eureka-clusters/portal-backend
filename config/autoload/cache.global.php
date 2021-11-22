<?php

$cache = [
    'adapter' => [
        'options' => [
            'server'    => [
                'host' => 'redis',
                'port' => 6379,
            ],
            'database'  => 1,
            'namespace' => 'pa-portal',
        ],
    ],
];

return [
    'application_config' => [
        'cache_options' => $cache,
    ],
    'translator'         => [
        'cache' => $cache,
    ],
];

