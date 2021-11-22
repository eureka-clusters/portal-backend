<?php

declare(strict_types=1);

return [
    'application_config' => [
        'cache_options' => [
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
        ],
    ],
];

