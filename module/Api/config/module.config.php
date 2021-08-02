<?php
/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

namespace Api;

use Admin\Entity\User;
use Api\V1\Rest;
use Cluster\Entity\Statistics\Partner;
use Cluster\Provider\PartnerProvider;
use Cluster\Provider\ProjectProvider;
use Cluster\Rest\Collection\PartnerCollection;
use Cluster\Rest\Collection\ProjectCollection;
use Contact\Entity\Contact;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\Stdlib;

$config = [
    'router'                       => [
        'routes' => [
            Rest\UserResource\MeListener::class             => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/[:id]',
                    'defaults' => [
                        'controller' => Rest\UserResource\MeListener::class,
                    ],
                ],
            ],
            Rest\ViewResource\ProjectListener::class        => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/view/project/[:identifier]',
                    'defaults' => [
                        'controller' => Rest\ViewResource\ProjectListener::class,
                    ],
                ],
            ],
            Rest\ViewResource\PartnerListener::class        => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/view/partner/[:identifier]',
                    'defaults' => [
                        'controller' => Rest\ViewResource\PartnerListener::class,
                    ],
                ],
            ],
            Rest\UpdateResource\ProjectListener::class      => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/api/update/project',
                    'defaults' => [
                        'controller' => Rest\UpdateResource\ProjectListener::class,
                    ],
                ],
            ],
            Rest\StatisticsResource\FacetsListener::class   => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/api/statistics/facets',
                    'defaults' => [
                        'controller' => Rest\StatisticsResource\FacetsListener::class,
                    ],
                ],
            ],
            Rest\StatisticsResource\ResultsListener::class  => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/api/statistics/results',
                    'defaults' => [
                        'controller' => Rest\StatisticsResource\ResultsListener::class,
                    ],
                ],
            ],
            Rest\StatisticsResource\DownloadListener::class => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/statistics/download/[:id]/[:filter]',
                    'defaults' => [
                        'controller' => Rest\StatisticsResource\DownloadListener::class,
                    ],
                ],
            ],
        ],
    ],
    'api-tools-rest'               => [
        Rest\UserResource\MeListener::class             => [
            'listener'                   => Rest\UserResource\MeListener::class,
            'route_name'                 => Rest\UserResource\MeListener::class,
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
        Rest\ViewResource\ProjectListener::class        => [
            'listener'                   => Rest\ViewResource\ProjectListener::class,
            'route_name'                 => Rest\ViewResource\ProjectListener::class,
            'route_identifier_name'      => 'identifier',
            'entity_http_methods'        => ['GET'],
            'collection_name'            => 'view_project',
            'collection_http_methods'    => [],
            'service_name'               => 'view_project',
            'entity_class'               => ProjectProvider::class,
            'collection_class'           => ProjectCollection::class,
            'page_size'                  => 25,
            'collection_query_whitelist' => [],
        ],
        Rest\ViewResource\PartnerListener::class        => [
            'listener'                   => Rest\ViewResource\PartnerListener::class,
            'route_name'                 => Rest\ViewResource\PartnerListener::class,
            'route_identifier_name'      => 'identifier',
            'entity_http_methods'        => ['GET'],
            'collection_name'            => 'view_partner',
            'collection_http_methods'    => [],
            'service_name'               => 'view_partner',
            'entity_class'               => PartnerProvider::class,
            'collection_class'           => PartnerCollection::class,
            'page_size'                  => 25,
            'collection_query_whitelist' => [],
        ],
        Rest\UpdateResource\ProjectListener::class      => [
            'listener'                   => Rest\UpdateResource\ProjectListener::class,
            'route_name'                 => Rest\UpdateResource\ProjectListener::class,
            'route_identifier_name'      => 'id',
            'entity_http_methods'        => [],
            'collection_name'            => 'update_project',
            'collection_http_methods'    => [
                'POST'
            ],
            'service_name'               => 'update_project',
            'entity_class'               => Partner::class,
            'collection_class'           => Partner::class,
            'page_size'                  => 25,
            'collection_query_whitelist' => [],
        ],
        Rest\StatisticsResource\FacetsListener::class   => [
            'listener'                   => Rest\StatisticsResource\FacetsListener::class,
            'route_name'                 => Rest\StatisticsResource\FacetsListener::class,
            'route_identifier_name'      => 'id',
            'entity_http_methods'        => [],
            'collection_name'            => 'facets',
            'collection_http_methods'    => ['GET'],
            'service_name'               => 'statistics_facets',
            'entity_class'               => Partner::class,
            'collection_class'           => Partner::class,
            'page_size'                  => 25,
            'collection_query_whitelist' => [
                'output',
                'filter'
            ],
        ],
        Rest\StatisticsResource\ResultsListener::class  => [
            'listener'                   => Rest\StatisticsResource\ResultsListener::class,
            'route_name'                 => Rest\StatisticsResource\ResultsListener::class,
            'route_identifier_name'      => 'id',
            'entity_http_methods'        => [],
            'collection_name'            => 'results',
            'collection_http_methods'    => ['GET'],
            'service_name'               => 'statistics_results',
            'entity_class'               => Partner::class,
            'collection_class'           => Partner::class,
            'page_size'                  => 25,
            'collection_query_whitelist' => [
                'output',
                'filter'
            ],
        ],
        Rest\StatisticsResource\DownloadListener::class => [
            'listener'                   => Rest\StatisticsResource\DownloadListener::class,
            'route_name'                 => Rest\StatisticsResource\DownloadListener::class,
            'route_identifier_name'      => 'id',
            'entity_http_methods'        => ['GET'],
            'collection_name'            => 'results',
            'collection_http_methods'    => [],
            'service_name'               => 'statistics_download',
            'entity_class'               => Partner::class,
            'collection_class'           => Partner::class,
            'page_size'                  => 25,
            'collection_query_whitelist' => [],
        ],
    ],
    'api-tools-mvc-auth'           => [
        'authorization' => [
            Rest\UserResource\MeListener::class          => [
                'entity' => [
                    'GET' => true,
                ],
            ],
            Rest\ViewResource\ProjectListener::class        => [
                'entity' => [
                    'GET' => true,
                ],
            ],
            Rest\ViewResource\PartnerListener::class        => [
                'entity' => [
                    'GET' => true,
                ],
            ],
            Rest\UpdateResource\ProjectListener::class      => [
                'collection' => [
                    'POST' => true,
                ],
            ],
            Rest\StatisticsResource\FacetsListener::class   => [
                'collection' => [
                    'GET' => true,
                ],
            ],
            Rest\StatisticsResource\ResultsListener::class  => [
                'collection' => [
                    'GET' => true,
                ],
            ],
            Rest\StatisticsResource\DownloadListener::class => [
                'entity' => [
                    'GET' => true,
                ],
            ],
        ],
    ],
    'api-tools-content-validation' => [
        Rest\UpdateResource\ProjectListener::class => [
            'input_filter' => Rest\UpdateResource\ProjectListener::class
        ]
    ],
    'input_filter_specs'           => [
        Rest\UpdateResource\ProjectListener::class => [
            [
                'required'      => true,
                'validators'    => [],
                'filters'       => [],
                'name'          => 'internal_identifier',
                'description'   => 'Please provide a value for the internal identifier',
                'field_type'    => 'string',
                'error_message' => 'Please provide a value for the internal identifier',
            ],
            [
                'required'      => true,
                'validators'    => [],
                'filters'       => [],
                'name'          => 'primary_cluster',
                'description'   => 'Please provide a value for the primary cluster',
                'field_type'    => 'string',
                'error_message' => 'Please provide a value for the primary cluster',
            ],

        ]
    ]
];


foreach (Stdlib\Glob::glob(__DIR__ . '/module.config.{,*}.php', Stdlib\Glob::GLOB_BRACE) as $file) {
    $config = Stdlib\ArrayUtils::merge($config, include $file);
}
return $config;
