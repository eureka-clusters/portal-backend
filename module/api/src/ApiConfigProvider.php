<?php

namespace Api;

use Admin\Entity\User;
use Api\V1\Rest;
use Api\V1\Rest\ListResource\OrganisationListener;
use Api\V1\Rest\ListResource\PartnerListener;
use Api\V1\Rest\ListResource\ProjectListener;
use Api\V1\Rest\UserResource\MeListener;
use Cluster\Entity\Project;
use Cluster\Entity\Project\Partner;
use Cluster\Provider\OrganisationProvider;
use Cluster\Provider\Project\PartnerProvider;
use Cluster\Provider\ProjectProvider;
use Cluster\Rest\Collection\PartnerCollection;
use Laminas\Paginator\Paginator;

class ApiConfigProvider
{
    public function getApiToolsRestConfig(): array
    {
        return [
            MeListener::class                                       => [
                'listener'                   => MeListener::class,
                'route_name'                 => MeListener::class,
                'route_identifier_name'      => 'id',
                'entity_http_methods'        => ['GET'],
                'collection_name'            => 'me',
                'collection_http_methods'    => [],
                'service_name'               => 'me',
                'entity_class'               => User::class,
                'collection_class'           => User::class,
                'page_size'                  => 25,
                'collection_query_whitelist' => [],
            ],
            ProjectListener::class                                  => [
                'listener'                   => ProjectListener::class,
                'route_name'                 => ProjectListener::class,
                'route_identifier_name'      => '',
                'entity_http_methods'        => [],
                'collection_name'            => 'projects',
                'collection_http_methods'    => ['GET'],
                'service_name'               => 'list_projects',
                'entity_class'               => ProjectProvider::class,
                'collection_class'           => Paginator::class,
                'page_size'                  => 25,
                'page_size_param'            => 'pageSize',
                'collection_query_whitelist' => [
                    'call',
                ],
            ],
            OrganisationListener::class                             => [
                'listener'                => OrganisationListener::class,
                'route_name'              => OrganisationListener::class,
                'route_identifier_name'   => '',
                'entity_http_methods'     => [],
                'collection_name'         => 'organisations',
                'collection_http_methods' => ['GET'],
                'service_name'            => 'list_organisations',
                'entity_class'            => OrganisationProvider::class,
                'collection_class'        => Paginator::class,
                'page_size'               => 25,
                'page_size_param'         => 'pageSize',
            ],
            PartnerListener::class                                  => [
                'listener'                   => PartnerListener::class,
                'route_name'                 => PartnerListener::class,
                'route_identifier_name'      => '',
                'entity_http_methods'        => [],
                'collection_name'            => 'partners',
                'collection_http_methods'    => ['GET'],
                'service_name'               => 'list_partners',
                'entity_class'               => PartnerProvider::class,
                'collection_class'           => Paginator::class,
                'page_size'                  => 12,
                'page_size_param'            => 'pageSize',
                'collection_query_whitelist' => [
                    'project',
                    'organisation',
                ],
            ],
            Rest\ViewResource\ProjectListener::class                => [
                'listener'                   => Rest\ViewResource\ProjectListener::class,
                'route_name'                 => Rest\ViewResource\ProjectListener::class,
                'route_identifier_name'      => 'slug',
                'entity_http_methods'        => ['GET'],
                'collection_name'            => 'project',
                'collection_http_methods'    => [],
                'service_name'               => 'view_project',
                'entity_class'               => ProjectProvider::class,
                'collection_class'           => Paginator::class,
                'page_size'                  => 25,
                'collection_query_whitelist' => [],
            ],
            Rest\ViewResource\OrganisationListener::class           => [
                'listener'                   => Rest\ViewResource\OrganisationListener::class,
                'route_name'                 => Rest\ViewResource\OrganisationListener::class,
                'route_identifier_name'      => 'slug',
                'entity_http_methods'        => ['GET'],
                'collection_name'            => 'organisation',
                'collection_http_methods'    => [],
                'service_name'               => 'view_project',
                'entity_class'               => OrganisationProvider::class,
                'collection_class'           => null,
                'page_size'                  => 25,
                'collection_query_whitelist' => [],
            ],
            Rest\ViewResource\PartnerListener::class                => [
                'listener'                   => Rest\ViewResource\PartnerListener::class,
                'route_name'                 => Rest\ViewResource\PartnerListener::class,
                'route_identifier_name'      => 'slug',
                'entity_http_methods'        => ['GET'],
                'collection_name'            => 'project',
                'collection_http_methods'    => [],
                'service_name'               => 'view_partner',
                'entity_class'               => PartnerProvider::class,
                'collection_class'           => PartnerCollection::class,
                'page_size'                  => 25,
                'collection_query_whitelist' => [],
            ],
            Rest\UpdateResource\ProjectListener::class              => [
                'listener'                   => Rest\UpdateResource\ProjectListener::class,
                'route_name'                 => Rest\UpdateResource\ProjectListener::class,
                'route_identifier_name'      => 'id',
                'entity_http_methods'        => [],
                'collection_name'            => 'update_project',
                'collection_http_methods'    => [
                    'POST',
                ],
                'service_name'               => 'update_project',
                'entity_class'               => Partner::class,
                'collection_class'           => Partner::class,
                'page_size'                  => 25,
                'collection_query_whitelist' => [],
            ],
            Rest\StatisticsResource\Facets\ProjectListener::class   => [
                'listener'                   => Rest\StatisticsResource\Facets\ProjectListener::class,
                'route_name'                 => Rest\StatisticsResource\Facets\ProjectListener::class,
                'route_identifier_name'      => 'id',
                'entity_http_methods'        => ['GET'],
                'collection_name'            => 'facets',
                'collection_http_methods'    => [],
                'service_name'               => 'statistics_facets_project',
                'entity_class'               => Project::class,
                'collection_class'           => Project::class,
                'page_size'                  => 25,
                'collection_query_whitelist' => [],
            ],
            Rest\StatisticsResource\Facets\PartnerListener::class   => [
                'listener'                   => Rest\StatisticsResource\Facets\PartnerListener::class,
                'route_name'                 => Rest\StatisticsResource\Facets\PartnerListener::class,
                'route_identifier_name'      => 'id',
                'entity_http_methods'        => ['GET'],
                'collection_name'            => 'facets',
                'collection_http_methods'    => [],
                'service_name'               => 'statistics_facets_partners',
                'entity_class'               => Partner::class,
                'collection_class'           => Partner::class,
                'page_size'                  => 25,
                'collection_query_whitelist' => [],
            ],
            Rest\StatisticsResource\Results\ProjectListener::class  => [
                'listener'                   => Rest\StatisticsResource\Results\ProjectListener::class,
                'route_name'                 => Rest\StatisticsResource\Results\ProjectListener::class,
                'route_identifier_name'      => 'id',
                'entity_http_methods'        => [],
                'collection_name'            => 'projects',
                'collection_http_methods'    => ['GET'],
                'service_name'               => 'statistics_results_projects',
                'entity_class'               => Project::class,
                'collection_class'           => Project::class,
                'page_size'                  => 25,
                'page_size_param'            => 'pageSize',
                'collection_query_whitelist' => [
                    'output',
                    'filter',
                    'sort',
                    'order',
                ],
            ],
            Rest\StatisticsResource\Results\PartnerListener::class  => [
                'listener'                   => Rest\StatisticsResource\Results\PartnerListener::class,
                'route_name'                 => Rest\StatisticsResource\Results\PartnerListener::class,
                'route_identifier_name'      => 'id',
                'entity_http_methods'        => [],
                'collection_name'            => 'partners',
                'collection_http_methods'    => ['GET'],
                'service_name'               => 'statistics_results_partners',
                'entity_class'               => Partner::class,
                'collection_class'           => Partner::class,
                'page_size'                  => 25,
                'page_size_param'            => 'pageSize',
                'collection_query_whitelist' => [
                    'output',
                    'filter',
                    'sort',
                    'order',
                ],
            ],
            Rest\StatisticsResource\Download\ProjectListener::class => [
                'listener'                   => Rest\StatisticsResource\Download\ProjectListener::class,
                'route_name'                 => Rest\StatisticsResource\Download\ProjectListener::class,
                'route_identifier_name'      => 'export_type',
                'entity_http_methods'        => ['GET'],
                'collection_name'            => 'projects',
                'collection_http_methods'    => [],
                'service_name'               => 'statistics_download_projects',
                'entity_class'               => Project::class,
                'collection_class'           => Project::class,
                'page_size'                  => 25,
                'collection_query_whitelist' => [],
            ],
            Rest\StatisticsResource\Download\PartnerListener::class => [
                'listener'                   => Rest\StatisticsResource\Download\PartnerListener::class,
                'route_name'                 => Rest\StatisticsResource\Download\PartnerListener::class,
                'route_identifier_name'      => 'export_type',
                'entity_http_methods'        => ['GET'],
                'collection_name'            => 'partners',
                'collection_http_methods'    => [],
                'service_name'               => 'statistics_download_partners',
                'entity_class'               => Partner::class,
                'collection_class'           => Partner::class,
                'page_size'                  => 25,
                'collection_query_whitelist' => [],
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
                Rest\StatisticsResource\Results\ProjectListener::class  => [
                    'collection' => [
                        'GET' => true,
                    ],
                ],
                Rest\StatisticsResource\Results\PartnerListener::class  => [
                    'collection' => [
                        'GET' => true,
                    ],
                ],
                Rest\StatisticsResource\Download\ProjectListener::class => [
                    'collection' => [
                        'GET' => true,
                    ],
                ],
                Rest\StatisticsResource\Download\PartnerListener::class => [
                    'collection' => [
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
                    'required'      => true,
                    'validators'    => [],
                    'filters'       => [],
                    'name'          => 'internalIdentifier',
                    'description'   => 'Please provide a value for the internal identifier',
                    'field_type'    => 'string',
                    'error_message' => 'Please provide a value for the internal identifier',
                ],
                [
                    'required'      => true,
                    'validators'    => [],
                    'filters'       => [],
                    'name'          => 'primaryCluster',
                    'description'   => 'Please provide a value for the primary cluster',
                    'field_type'    => 'string',
                    'error_message' => 'Please provide a value for the primary cluster',
                ],
            ],
        ];
    }
}
