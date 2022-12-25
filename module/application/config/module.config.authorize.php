<?php

declare(strict_types=1);

use Admin\Entity\Role;
use BjyAuthorize\Guard\Route;

return [
    'bjyauthorize' => [
        'guards' => [
            Route::class => [
                ['route' => 'home', 'roles' => []],
                ['route' => 'oauth/authorize', 'roles' => [Role::ROLE_USER]],
                ['route' => 'oauth/resource', 'roles' => [Role::ROLE_USER]],
                ['route' => 'oauth/revoke', 'roles' => [Role::ROLE_USER]],
                ['route' => 'oauth/code', 'roles' => [Role::ROLE_USER]],
                ['route' => 'oauth', 'roles' => []],
                ['route' => 'oauth2/login', 'roles' => []],
                ['route' => 'oauth2/callback', 'roles' => []],
                ['route' => 'oauth2/refresh', 'roles' => []],
            ],
        ],
    ],
];
