<?php

namespace Api;

use Admin\Provider\UserProvider;
use Admin\Service\UserService;
use Api\Service\OAuthService;
use Api\V1\Rest;
use Api\V1\Rest\ListResource\OrganisationListener;
use Api\V1\Rest\ListResource\PartnerListener;
use Api\V1\Rest\ListResource\ProjectListener;
use Api\V1\Rest\UserResource\MeListener;
use Cluster\Provider\OrganisationProvider;
use Cluster\Provider\Project\PartnerProvider;
use Cluster\Provider\Project\PartnerYearProvider;
use Cluster\Provider\ProjectProvider;
use Cluster\Provider\SearchResultProvider;
use Cluster\Service\OrganisationService;
use Cluster\Service\Project\PartnerService;
use Cluster\Service\Project\VersionService;
use Cluster\Service\ProjectService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;

final class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    public function getDependencyConfig(): array
    {
        return [
            'factories' => [
                OAuthService::class                                     => ConfigAbstractFactory::class,
                MeListener::class                                       => ConfigAbstractFactory::class,
                ProjectListener::class                                  => ConfigAbstractFactory::class,
                OrganisationListener::class                             => ConfigAbstractFactory::class,
                PartnerListener::class                                  => ConfigAbstractFactory::class,
                Rest\SearchResource\ResultListener::class               => ConfigAbstractFactory::class,
                Rest\ViewResource\ProjectListener::class                => ConfigAbstractFactory::class,
                Rest\ViewResource\OrganisationListener::class           => ConfigAbstractFactory::class,
                Rest\ViewResource\PartnerListener::class                => ConfigAbstractFactory::class,
                Rest\UpdateResource\ProjectListener::class              => ConfigAbstractFactory::class,
                Rest\StatisticsResource\Results\ProjectListener::class  => ConfigAbstractFactory::class,
                Rest\StatisticsResource\Results\PartnerListener::class  => ConfigAbstractFactory::class,
                Rest\StatisticsResource\Facets\ProjectListener::class   => ConfigAbstractFactory::class,
                Rest\StatisticsResource\Facets\PartnerListener::class   => ConfigAbstractFactory::class,
                Rest\StatisticsResource\Download\ProjectListener::class => ConfigAbstractFactory::class,
                Rest\StatisticsResource\Download\PartnerListener::class => ConfigAbstractFactory::class,
            ],
        ];
    }

    public function getDoctrineConfig(): array
    {
        return [
            'driver' => [
                'api_attribute_driver' => [
                    'class' => AttributeDriver::class,
                    'paths' => [
                        __DIR__ . '/../src/Entity/',
                    ],
                ],
                'orm_default'          => [
                    'drivers' => [
                        'Api\\Entity' => 'api_attribute_driver',
                    ],
                ],
            ],
        ];
    }

    public function getConfigAbstractFactory(): array
    {
        return [
            MeListener::class                                       => [
                UserService::class,
                UserProvider::class,
            ],
            OrganisationListener::class                             => [
                OrganisationService::class,
                OrganisationProvider::class,
            ],
            ProjectListener::class                                  => [
                ProjectService::class,
                UserService::class,
                ProjectProvider::class,
            ],
            PartnerListener::class                                  => [
                PartnerService::class,
                ProjectService::class,
                OrganisationService::class,
                UserService::class,
                PartnerProvider::class,
            ],
            Rest\SearchResource\ResultListener::class               => [
                ProjectService::class,
                OrganisationService::class,
                UserService::class,
                SearchResultProvider::class,
            ],
            Rest\ViewResource\ProjectListener::class                => [
                ProjectService::class,
                UserService::class,
                ProjectProvider::class,
            ],
            Rest\ViewResource\OrganisationListener::class           => [
                OrganisationService::class,
                OrganisationProvider::class,
            ],
            Rest\ViewResource\PartnerListener::class                => [
                PartnerService::class,
                PartnerProvider::class,
            ],
            Rest\UpdateResource\ProjectListener::class              => [
                ProjectService::class,
                VersionService::class,
                PartnerService::class,
                EntityManager::class,
            ],
            Rest\StatisticsResource\Facets\ProjectListener::class   => [
                ProjectService::class,
                UserService::class,
            ],
            Rest\StatisticsResource\Facets\PartnerListener::class   => [
                PartnerService::class,
                UserService::class,
            ],
            Rest\StatisticsResource\Results\ProjectListener::class  => [
                ProjectService::class,
                UserService::class,
                ProjectProvider::class,
            ],
            Rest\StatisticsResource\Results\PartnerListener::class  => [
                PartnerService::class,
                UserService::class,
                PartnerProvider::class,
                PartnerYearProvider::class
            ],
            Rest\StatisticsResource\Download\ProjectListener::class => [
                ProjectService::class,
                UserService::class,
                TranslatorInterface::class,
                ProjectProvider::class,
            ],
            Rest\StatisticsResource\Download\PartnerListener::class => [
                PartnerService::class,
                UserService::class,
                TranslatorInterface::class,
                PartnerProvider::class,
                PartnerYearProvider::class
            ],
            OAuthService::class                                     => [
                EntityManager::class,
                TranslatorInterface::class
            ],
        ];
    }

    public function getRouteConfig(): array
    {
        return [
            'routes' => [
                MeListener::class                                       => [
                    'type'    => Segment::class,
                    'options' => [
                        'route'    => '/api/[:id]',
                        'defaults' => [
                            'controller' => MeListener::class,
                        ],
                    ],
                ],
                ProjectListener::class                                  => [
                    'type'    => Literal::class,
                    'options' => [
                        'route'    => '/api/list/project',
                        'defaults' => [
                            'controller' => ProjectListener::class,
                        ],
                    ],
                ],
                OrganisationListener::class                             => [
                    'type'    => Literal::class,
                    'options' => [
                        'route'    => '/api/list/organisation',
                        'defaults' => [
                            'controller' => OrganisationListener::class,
                        ],
                    ],
                ],
                PartnerListener::class                                  => [
                    'type'    => Literal::class,
                    'options' => [
                        'route'    => '/api/list/partner',
                        'defaults' => [
                            'controller' => PartnerListener::class,
                        ],
                    ],
                ],
                Rest\SearchResource\ResultListener::class               => [
                    'type'    => Literal::class,
                    'options' => [
                        'route'    => '/api/search/result',
                        'defaults' => [
                            'controller' => Rest\SearchResource\ResultListener::class,
                        ],
                    ],
                ],
                Rest\ViewResource\ProjectListener::class                => [
                    'type'    => Segment::class,
                    'options' => [
                        'route'    => '/api/view/project/[:slug]',
                        'defaults' => [
                            'controller' => Rest\ViewResource\ProjectListener::class,
                        ],
                    ],
                ],
                Rest\ViewResource\OrganisationListener::class           => [
                    'type'    => Segment::class,
                    'options' => [
                        'route'    => '/api/view/organisation/[:slug]',
                        'defaults' => [
                            'controller' => Rest\ViewResource\OrganisationListener::class,
                        ],
                    ],
                ],
                Rest\ViewResource\PartnerListener::class                => [
                    'type'    => Segment::class,
                    'options' => [
                        'route'    => '/api/view/partner/[:slug]',
                        'defaults' => [
                            'controller' => Rest\ViewResource\PartnerListener::class,
                        ],
                    ],
                ],
                Rest\UpdateResource\ProjectListener::class              => [
                    'type'    => Literal::class,
                    'options' => [
                        'route'    => '/api/update/project',
                        'defaults' => [
                            'controller' => Rest\UpdateResource\ProjectListener::class,
                        ],
                    ],
                ],
                Rest\StatisticsResource\Facets\ProjectListener::class   => [
                    'type'    => Segment::class,
                    'options' => [
                        'route'    => '/api/statistics/facets/project/[:id]',
                        'defaults' => [
                            'controller' => Rest\StatisticsResource\Facets\ProjectListener::class,
                        ],
                    ],
                ],
                Rest\StatisticsResource\Facets\PartnerListener::class   => [
                    'type'    => Segment::class,
                    'options' => [
                        'route'    => '/api/statistics/facets/partner/[:id]',
                        'defaults' => [
                            'controller' => Rest\StatisticsResource\Facets\PartnerListener::class,
                        ],
                    ],
                ],
                Rest\StatisticsResource\Results\ProjectListener::class  => [
                    'type'    => Literal::class,
                    'options' => [
                        'route'    => '/api/statistics/results/project',
                        'defaults' => [
                            'controller' => Rest\StatisticsResource\Results\ProjectListener::class,
                        ],
                    ],
                ],
                Rest\StatisticsResource\Results\PartnerListener::class  => [
                    'type'    => Literal::class,
                    'options' => [
                        'route'    => '/api/statistics/results/partner',
                        'defaults' => [
                            'controller' => Rest\StatisticsResource\Results\PartnerListener::class,
                        ],
                    ],
                ],
                Rest\StatisticsResource\Download\ProjectListener::class => [
                    'type'    => Segment::class,
                    'options' => [
                        'route'    => '/api/statistics/results/project/download/[:export_type]',
                        'defaults' => [
                            'controller' => Rest\StatisticsResource\Download\ProjectListener::class,
                        ],
                    ],
                ],
                Rest\StatisticsResource\Download\PartnerListener::class => [
                    'type'    => Segment::class,
                    'options' => [
                        'route'    => '/api/statistics/results/partner/download/[:export_type]',
                        'defaults' => [
                            'controller' => Rest\StatisticsResource\Download\PartnerListener::class,
                        ],
                    ],
                ],
            ],
        ];
    }
}
