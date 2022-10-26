<?php

declare(strict_types=1);

namespace Deeplink;


use BjyAuthorize\Guard\Route;

return [
    'bjyauthorize' => [
        /* Currently, only controller and route guards exist
         */
        'guards' => [
            /* If this guard is specified here (i.e. it is enabled), it will block
             * access to all routes unless they are specified here.
             */
            Route::class => [
                ['route' => 'deeplink', 'roles' => []],
                ['route' => 'zfcadmin/deeplink/target/list', 'roles' => [1]],
                ['route' => 'zfcadmin/deeplink/target/new', 'roles' => [1]],
                ['route' => 'zfcadmin/deeplink/target/view', 'roles' => [1]],
                ['route' => 'zfcadmin/deeplink/target/edit', 'roles' => [1]],
            ],
        ],
    ],
];
