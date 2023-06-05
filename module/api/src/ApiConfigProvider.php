<?php

declare(strict_types=1);

namespace Api;

use Api\V1\Rest;
use Api\V1\Rest\ListResource\OrganisationListener;
use Api\V1\Rest\ListResource\PartnerListener;
use Api\V1\Rest\ListResource\ProjectListener;
use Api\V1\Rest\SearchResource\ResultListener;
use Api\V1\Rest\UserResource\MeListener;

final class ApiConfigProvider
{
    public function getApiToolsRestConfig(): array
    {
        return [
            MeListener::class                                       => [
                'listener'              => MeListener::class,
                'route_name'            => MeListener::class,
                'route_identifier_name' => 'id',
                'entity_http_methods'   => ['GET'],
            ],
            Rest\ListResource\ServiceListener::class                => [
                'listener'                => Rest\ListResource\ServiceListener::class,
                'route_name'              => Rest\ListResource\ServiceListener::class,
                'route_identifier_name'   => '',
                'collection_http_methods' => ['GET'],
                'page_size_param'         => 'pageSize',
                'page_size'               => 25,
            ],
            ProjectListener::class                                  => [
                'listener'                   => ProjectListener::class,
                'route_name'                 => ProjectListener::class,
                'route_identifier_name'      => '',
                'collection_http_methods'    => ['GET'],
                'page_size'                  => 25,
                'page_size_param'            => 'pageSize',
                'collection_query_whitelist' => [
                    'query',
                    'order',
                    'direction',
                    'filter'
                ],
            ],
            OrganisationListener::class                             => [
                'listener'                   => OrganisationListener::class,
                'route_name'                 => OrganisationListener::class,
                'route_identifier_name'      => '',
                'entity_http_methods'        => [],
                'collection_http_methods'    => ['GET'],
                'page_size'                  => 25,
                'page_size_param'            => 'pageSize',
                'collection_query_whitelist' => [
                    'query',
                    'order',
                    'direction',
                    'filter'
                ],
            ],
            PartnerListener::class                                  => [
                'listener'                   => PartnerListener::class,
                'route_name'                 => PartnerListener::class,
                'route_identifier_name'      => '',
                'entity_http_methods'        => [],
                'collection_http_methods'    => ['GET'],
                'page_size'                  => 25,
                'page_size_param'            => 'pageSize',
                'collection_query_whitelist' => [
                    'project',
                    'organisation',
                    'order',
                    'direction',
                    'filter'
                ],
            ],
            ResultListener::class                                   => [
                'listener'                   => ResultListener::class,
                'route_name'                 => ResultListener::class,
                'route_identifier_name'      => 'search',
                'entity_http_methods'        => [],
                'collection_http_methods'    => ['GET'],
                'page_size'                  => 25,
                'page_size_param'            => 'pageSize',
                'collection_query_whitelist' => [
                    'query',
                    'order',
                    'direction',
                ],
            ],
            Rest\ViewResource\ProjectListener::class                => [
                'listener'              => Rest\ViewResource\ProjectListener::class,
                'route_name'            => Rest\ViewResource\ProjectListener::class,
                'route_identifier_name' => 'slug',
                'entity_http_methods'   => ['GET'],
                'page_size'             => 25,
            ],
            Rest\ViewResource\OrganisationListener::class           => [
                'listener'              => Rest\ViewResource\OrganisationListener::class,
                'route_name'            => Rest\ViewResource\OrganisationListener::class,
                'route_identifier_name' => 'slug',
                'entity_http_methods'   => ['GET'],
                'page_size'             => 25,
            ],
            Rest\ViewResource\PartnerListener::class                => [
                'listener'              => Rest\ViewResource\PartnerListener::class,
                'route_name'            => Rest\ViewResource\PartnerListener::class,
                'route_identifier_name' => 'slug',
                'entity_http_methods'   => ['GET'],
                'page_size'             => 25,
            ],
            Rest\UpdateResource\ProjectListener::class              => [
                'listener'                => Rest\UpdateResource\ProjectListener::class,
                'route_name'              => Rest\UpdateResource\ProjectListener::class,
                'route_identifier_name'   => 'id',
                'collection_http_methods' => ['POST'],
            ],
            Rest\StatisticsResource\Facets\ProjectListener::class   => [
                'listener'              => Rest\StatisticsResource\Facets\ProjectListener::class,
                'route_name'            => Rest\StatisticsResource\Facets\ProjectListener::class,
                'route_identifier_name' => 'filter',
                'entity_http_methods'   => ['GET'],
            ],
            Rest\StatisticsResource\Facets\PartnerListener::class   => [
                'listener'              => Rest\StatisticsResource\Facets\PartnerListener::class,
                'route_name'            => Rest\StatisticsResource\Facets\PartnerListener::class,
                'route_identifier_name' => 'filter',
                'entity_http_methods'   => ['GET'],
            ],
            Rest\StatisticsResource\Download\ProjectListener::class => [
                'listener'              => Rest\StatisticsResource\Download\ProjectListener::class,
                'route_name'            => Rest\StatisticsResource\Download\ProjectListener::class,
                'route_identifier_name' => 'filter',
                'entity_http_methods'   => ['GET'],
            ],
            Rest\StatisticsResource\Download\PartnerListener::class => [
                'listener'              => Rest\StatisticsResource\Download\PartnerListener::class,
                'route_name'            => Rest\StatisticsResource\Download\PartnerListener::class,
                'route_identifier_name' => 'filter',
                'entity_http_methods'   => ['GET'],
            ],
        ];
    }

    public function getApiToolsMvcConfig(): array
    {
        return [
            'authorization' => [
                MeListener::class                                       => [
                    'entity' => [
                        'GET' => true,
                    ],
                ],
                Rest\ListResource\ServiceListener::class                => [
                    'collection' => [
                        'GET' => false,
                    ],
                ],
                ProjectListener::class                                  => [
                    'collection' => [
                        'GET' => true,
                    ],
                ],
                PartnerListener::class                                  => [
                    'collection' => [
                        'GET' => true,
                    ],
                ],
                OrganisationListener::class                             => [
                    'collection' => [
                        'GET' => true,
                    ],
                ],
                ResultListener::class                                   => [
                    'collection' => [
                        'GET' => true,
                    ],
                ],
                Rest\ViewResource\ProjectListener::class                => [
                    'entity' => [
                        'GET' => true,
                    ],
                ],
                Rest\ViewResource\OrganisationListener::class           => [
                    'entity' => [
                        'GET' => true,
                    ],
                ],
                Rest\ViewResource\PartnerListener::class                => [
                    'entity' => [
                        'GET' => true,
                    ],
                ],
                Rest\UpdateResource\ProjectListener::class              => [
                    'collection' => [
                        'POST' => true,
                    ],
                ],
                Rest\StatisticsResource\Facets\ProjectListener::class   => [
                    'entity' => [
                        'GET' => true,
                    ],
                ],
                Rest\StatisticsResource\Facets\PartnerListener::class   => [
                    'entity' => [
                        'GET' => true,
                    ],
                ],
                Rest\StatisticsResource\Download\ProjectListener::class => [
                    'entity' => [
                        'GET' => true,
                    ],
                ],
                Rest\StatisticsResource\Download\PartnerListener::class => [
                    'entity' => [
                        'GET' => true,
                    ],
                ],
            ],
        ];
    }

    public function getApiToolsContentValidationConfig(): array
    {
        return [
            Rest\UpdateResource\ProjectListener::class => [
                'input_filter' => Rest\UpdateResource\ProjectListener::class,
            ],
        ];
    }

    public function getApiToolsInputFilterSpecsConfig(): array
    {
        return [
            Rest\UpdateResource\ProjectListener::class => [
                [
                    'required'   => true,
                    'validators' => [],
                    'filters'    => [],
                    'name'       => 'file',
                    'type'       => \Laminas\InputFilter\FileInput::class,
                ]
            ],
        ];
    }
}
