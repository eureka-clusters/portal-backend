<?php

declare(strict_types=1);

return [
    'cache' => [
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
];