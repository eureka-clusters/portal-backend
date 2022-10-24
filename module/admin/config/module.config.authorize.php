<?php

declare(strict_types=1);

use Admin\Entity\Role;
use BjyAuthorize\Guard\Route;

return [
    'bjyauthorize' => [
        'guards' => [
            Route::class => [
                ['route' => 'zfcadmin', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/index', 'roles' => [Role::ROLE_ADMIN]],

                ['route' => 'zfcadmin/oauth2/scope/list', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/oauth2/scope/new', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/oauth2/scope/view', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/oauth2/scope/edit', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/oauth2/client/list', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/oauth2/client/new', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/oauth2/client/view', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/oauth2/client/edit', 'roles' => [Role::ROLE_ADMIN]],

                ['route' => 'zfcadmin/role/view', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/role/new', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/role/list', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/role/edit', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/support/view', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/support/new', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/support/list', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/support/edit', 'roles' => [Role::ROLE_ADMIN]],

                ['route' => 'zfcadmin/user/view', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/user/list', 'roles' => [Role::ROLE_ADMIN]],
            ],
        ],
    ],
];
