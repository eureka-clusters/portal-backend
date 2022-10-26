<?php

declare(strict_types=1);

use Deeplink\Entity\Target;
use Deeplink\Navigation\Invokable\TargetLabel;

return [
    'navigation' => [
        'admin' => [
            'config' => [
                'pages' => [
                    'deeplink-targets' => [
                        'label' => _("txt-deeplink-targets"),
                        'route' => 'zfcadmin/deeplink/target/list',
                        'pages' => [
                            'deeplink' => [
                                'route'   => 'zfcadmin/deeplink/target/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Target::class,
                                    ],
                                    'invokables' => [
                                        TargetLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'edit' => [
                                        'label'   => _('txt-nav-edit'),
                                        'route'   => 'zfcadmin/deeplink/target/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Target::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
