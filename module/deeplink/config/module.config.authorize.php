<?php

declare(strict_types=1);

namespace Deeplink;

use Admin\Entity\Role;
use BjyAuthorize\Guard\Route;

return [
    'bjyauthorize' => [
        /* Currently, only controller and route guards exist
         */
        'guards' => [
            Route::class => [
                ['route' => 'deeplink', 'roles' => []],
                ['route' => 'zfcadmin/deeplink/target/list', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/deeplink/target/new', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/deeplink/target/view', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/deeplink/target/edit', 'roles' => [Role::ROLE_ADMIN]],
            ],
        ],
    ],
];
