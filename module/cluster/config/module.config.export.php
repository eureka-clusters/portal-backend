<?php

declare(strict_types=1);

namespace Cluster;

use Cluster\Export\ProjectColumns;

return [
    'jield_export' => [
        'entities' => [
            'project' => ['columns' => ProjectColumns::class],
        ],
    ]
];
