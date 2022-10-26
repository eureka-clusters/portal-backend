<?php

declare(strict_types=1);

use Admin\Entity\Role;
use BjyAuthorize\Guard\Route;

return [
    'bjyauthorize' => [
        'guards' => [
            Route::class => [
                ['route' => 'user/login', 'roles' => [Role::ROLE_PUBLIC]],
                ['route' => 'user/logout', 'roles' => []],
                ['route' => 'user/lost-password', 'roles' => [Role::ROLE_PUBLIC]],
                ['route' => 'user/change-password', 'roles' => [Role::ROLE_USER]],

            ],
        ],
    ],
];
