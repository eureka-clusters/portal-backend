<?php

declare(strict_types=1);

use Deeplink\Entity\Target;
use Deeplink\Navigation\Invokable\TargetLabel;

return [
    'navigation' => [
        'default' => [
            'mailing' => [
                'pages' => [
                    'deeplink-targets' => [
                        'label' => _('txt-deeplink-targets'),
                        'route' => 'zfcadmin/deeplink/target/list',
                        'pages' => [
                            'view' => [
                                'label'  => 'txt-nav-dee-link-target-view',
                                'route'  => 'zfcadmin/deeplink/target/view',
                                'params' => [
                                    'entities'   => [
                                        'id' => Target::class,
                                    ],
                                    'invokables' => [
                                        TargetLabel::class,
                                    ],
                                ],
                                'pages'  => [
                                    'edit' => [
                                        'label'  => _('txt-nav-edit'),
                                        'route'  => 'zfcadmin/deeplink/target/edit',
                                        'params' => [
                                            'entities' => [
                                                'id' => Target::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'new'  => [
                                'label' => _('txt-nav-new-deeplink-target'),
                                'route' => 'zfcadmin/deeplink/target/new',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
