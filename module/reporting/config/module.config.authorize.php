<?php

declare(strict_types=1);

use Admin\Entity\Role;
use BjyAuthorize\Guard\Route;

return [
    'bjyauthorize' => [
        'guards' => [
            Route::class => [
                ['route' => 'zfcadmin/reporting/index', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/reporting/download/blob', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/reporting/storage-location/list', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/reporting/storage-location/new', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/reporting/storage-location/view', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/reporting/storage-location/edit', 'roles' => [Role::ROLE_ADMIN]],
            ],
        ],
    ],
];
