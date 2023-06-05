<?php

declare(strict_types=1);

namespace Cluster;

use Cluster\Entity\Cluster\Group;
use Cluster\Entity\Project;
use Cluster\Navigation\Invokable\Cluster\GroupLabel;
use Cluster\Navigation\Invokable\ProjectLabel;

return [
    'navigation' => [
        'default' => [
            'uses-and-groups' => [
                'label'     => _('txt-clusters-and-projects'),
                'resource'  => 'route/zfcadmin/project/list',
                'privilege' => 'list',
                'uri'       => '#',
                'pages'     => [
                    'cluster-group' => [
                        'label' => _('txt-cluster-group-list'),
                        'route' => 'zfcadmin/cluster/group/list',
                        'pages' => [
                            'view' => [
                                'route'  => 'zfcadmin/cluster/group/view',
                                'params' => [
                                    'entities'   => [
                                        'id' => Group::class,
                                    ],
                                    'invokables' => [
                                        GroupLabel::class,
                                    ],
                                ],
                                'pages'  => [
                                    'edit' => [
                                        'label'  => _('txt-edit-cluster-group'),
                                        'route'  => 'zfcadmin/cluster/group/edit',
                                        'params' => [
                                            'entities' => [
                                                'id' => Group::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'new'  => [
                                'label' => _('txt-new-cluster-group'),
                                'route' => 'zfcadmin/cluster/group/new',
                            ],
                        ],
                    ],
                    'project'       => [
                        'label' => _('txt-project-list'),
                        'route' => 'zfcadmin/project/list',
                        'pages' => [
                            'view' => [
                                'route'  => 'zfcadmin/project/view',
                                'params' => [
                                    'entities'   => [
                                        'id' => Project::class,
                                    ],
                                    'invokables' => [
                                        ProjectLabel::class,
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
