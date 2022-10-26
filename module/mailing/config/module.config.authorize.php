<?php

declare(strict_types=1);

use Admin\Entity\Role;
use BjyAuthorize\Guard\Route;

return [
    'bjyauthorize' => [
        'guards' => [
            Route::class => [
                ['route' => 'zfcadmin/mailing/template/list', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/mailing/template/view', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/mailing/template/edit', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/mailing/template/new', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/mailing/sender/list', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/mailing/sender/view', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/mailing/sender/edit', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/mailing/sender/new', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/mailing/mailer/list', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/mailing/mailer/view', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/mailing/mailer/edit', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/mailing/mailer/new', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/mailing/transactional/list', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/mailing/transactional/view', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/mailing/transactional/edit', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/mailing/transactional/new', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/mailing/email/list', 'roles' => [Role::ROLE_ADMIN]],
                ['route' => 'zfcadmin/mailing/email/view', 'roles' => [Role::ROLE_ADMIN]],
            ],
        ],
    ],
];
