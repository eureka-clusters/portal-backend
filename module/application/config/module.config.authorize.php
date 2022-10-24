<?php

declare(strict_types=1);

use Admin\Entity\Role;
use BjyAuthorize\Guard\Route;

return [
    'bjyauthorize' => [
        'guards' => [
            Route::class => [
                ['route' => 'home', 'roles' => []],
                ['route' => 'oauth2/login', 'roles' => []],
                ['route' => 'oauth2/callback', 'roles' => []],

            ],
        ],
    ],
];
