<?php

declare(strict_types=1);

use Admin\Entity\Role;
use BjyAuthorize\Guard\Route;

return [
    'bjyauthorize' => [
        'guards' => [
            Route::class => [
                ['route' => 'zfcadmin/cluster/group/list', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/cluster/group/new', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/cluster/group/view', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/cluster/group/edit', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/project/list', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/project/view', 'roles' => [Role::ROLE_ADMIN]],
            ],
        ],
    ],
];
