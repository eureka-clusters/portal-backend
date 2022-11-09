<?php

declare(strict_types=1);

use BjyAuthorize\Guard\Route;

return [
    'bjyauthorize' => [
        'guards' => [
            Route::class => [
                ['route' => 'home', 'roles' => []],
                ['route' => 'oauth', 'roles' => []],
                ['route' => 'oauth2/login', 'roles' => []],
                ['route' => 'oauth2/callback', 'roles' => []],
                ['route' => 'oauth2/login', 'roles' => []],
                ['route' => 'oauth2/callback', 'roles' => []],
            ],
        ],
    ],
];
