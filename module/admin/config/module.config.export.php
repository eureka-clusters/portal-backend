<?php

declare(strict_types=1);

namespace Admin;

use Admin\Export\UserColumns;

return [
    'jield_export' => [
        'entities' => [
            'user' => ['columns' => UserColumns::class],
        ],
    ]
];
