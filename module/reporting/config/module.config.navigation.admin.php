<?php

declare(strict_types=1);

return [
    'navigation' => [
        'default' => [
            'reporting' => [
                'label'    => _('txt-reporting'),
                'uri'      => '#',
                'resource' => 'route/zfcadmin/reporting/index',
                'pages'    => [
                    'index'            => [
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
                                        'id' => \Reporting\Entity\StorageLocation::class,
                                    ],
                                    'invokables' => [
                                        \Reporting\Navigation\Invokable\StorageLocationLabel::class
                                    ],
                                ],
                                'pages'  => [
                                    'edit' => [
                                        'label'  => _('txt-nav-edit'),
                                        'route'  => 'zfcadmin/reporting/storage-location/edit',
                                        'params' => [
                                            'entities' => [
                                                'id' => \Reporting\Entity\StorageLocation::class,
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
            ],
        ],
    ],
];
