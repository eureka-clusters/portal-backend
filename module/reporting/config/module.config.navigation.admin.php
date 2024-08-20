<?php

declare(strict_types=1);

use Reporting\Entity\StorageLocation;
use Reporting\Navigation\Invokable\StorageLocationLabel;

return [
    'navigation' => [
        'admin' => [
            'management' => [
                'pages' => [
                    'reporting'        => [
                        'label' => _("txt-reporting"),
                        'route' => 'zfcadmin/reporting/index',
                    ],
                    'storage-location' => [
                        'label' => _('txt-nav-storage-location-list'),
                        'route' => 'zfcadmin/reporting/storage-location/list',
                        'pages' => [
                            'view' => [
                                'route'  => 'zfcadmin/reporting/storage-location/view',
                                'params' => [
                                    'entities'   => [
                                        'id' => StorageLocation::class,
                                    ],
                                    'invokables' => [
                                        StorageLocationLabel::class
                                    ],
                                ],
                                'pages'  => [
                                    'edit' => [
                                        'label'  => _('txt-nav-edit'),
                                        'route'  => 'zfcadmin/reporting/storage-location/edit',
                                        'params' => [
                                            'entities' => [
                                                'id' => StorageLocation::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'new'  => [
                                'route' => 'zfcadmin/reporting/storage-location/new',
                                'label' => _('txt-new-storage-location'),
                            ],
                        ],
                    ],
                ],
            ]
        ],
    ]
];
