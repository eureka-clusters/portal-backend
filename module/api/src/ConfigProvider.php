<?php

declare(strict_types=1);

namespace Api;

use Admin\Provider\UserProvider;
use Admin\Service\OAuth2Service;
use Admin\Service\UserService;
use Api\Provider\OAuth\ServiceProvider;
use Api\V1\Rest;
use Api\V1\Rest\ListResource\OrganisationListener;
use Api\V1\Rest\ListResource\PartnerListener;
use Api\V1\Rest\ListResource\ProjectListener;
use Api\V1\Rest\SearchResource\ResultListener;
use Api\V1\Rest\UserResource\MeListener;
use Application\Options\ModuleOptions;
use BjyAuthorize\Guard\Route;
use Cluster\Provider\OrganisationProvider;
use Cluster\Provider\Project\PartnerProvider;
use Cluster\Provider\Project\PartnerYearProvider;
use Cluster\Provider\Project\VersionProvider;
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
                MeListener::class                                       => ConfigAbstractFactory::class,
                Rest\ListResource\ServiceListener::class                => ConfigAbstractFactory::class,
                ProjectListener::class                                  => ConfigAbstractFactory::class,
                Rest\ListResource\Project\VersionListener::class        => ConfigAbstractFactory::class,
                OrganisationListener::class                             => ConfigAbstractFactory::class,
                PartnerListener::class                                  => ConfigAbstractFactory::class,
                ResultListener::class                                   => ConfigAbstractFactory::class,
                Rest\ViewResource\ProjectListener::class                => ConfigAbstractFactory::class,
                Rest\ViewResource\OrganisationListener::class           => ConfigAbstractFactory::class,
                Rest\ViewResource\PartnerListener::class                => ConfigAbstractFactory::class,
                Rest\UpdateResource\ProjectListener::class              => ConfigAbstractFactory::class,
                Rest\StatisticsResource\Facets\ProjectListener::class   => ConfigAbstractFactory::class,
                Rest\StatisticsResource\Facets\PartnerListener::class   => ConfigAbstractFactory::class,
                Rest\StatisticsResource\Download\ProjectListener::class => ConfigAbstractFactory::class,
                Rest\StatisticsResource\Download\PartnerListener::class => ConfigAbstractFactory::class,
                ServiceProvider::class                                  => ConfigAbstractFactory::class,
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
            Rest\ListResource\ServiceListener::class                => [
                OAuth2Service::class,
                ServiceProvider::class,
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
            Rest\ListResource\Project\VersionListener::class        => [
                VersionService::class,
                ProjectService::class,
                UserService::class,
                VersionProvider::class,
            ],
            PartnerListener::class                                  => [
                PartnerService::class,
                ProjectService::class,
                OrganisationService::class,
                VersionService::class,
                UserService::class,
                PartnerProvider::class,
                PartnerYearProvider::class
            ],
            ResultListener::class                                   => [
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
                PartnerYearProvider::class,
            ],
            ServiceProvider::class                                  => [
                'ViewHelperManager',
                ModuleOptions::class,
            ],
        ];
    }

    public function getGuardConfig(): array
    {
        return [
            'guards' => [
                Route::class => [
                    ['route' => MeListener::class, 'roles' => []],
                    ['route' => Rest\ListResource\ServiceListener::class, 'roles' => []],
                    ['route' => ProjectListener::class, 'roles' => []],
                    ['route' => Rest\ListResource\Project\VersionListener::class, 'roles' => []],
                    ['route' => OrganisationListener::class, 'roles' => []],
                    ['route' => PartnerListener::class, 'roles' => []],
                    ['route' => ResultListener::class, 'roles' => []],
                    ['route' => Rest\ViewResource\ProjectListener::class, 'roles' => []],
                    ['route' => Rest\ViewResource\OrganisationListener::class, 'roles' => []],
                    ['route' => Rest\ViewResource\PartnerListener::class, 'roles' => []],
                    ['route' => Rest\UpdateResource\ProjectListener::class, 'roles' => []],
                    ['route' => Rest\StatisticsResource\Facets\ProjectListener::class, 'roles' => []],
                    ['route' => Rest\StatisticsResource\Facets\PartnerListener::class, 'roles' => []],
                    ['route' => Rest\StatisticsResource\Download\ProjectListener::class, 'roles' => []],
                    ['route' => Rest\StatisticsResource\Download\PartnerListener::class, 'roles' => []],
                ],
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
                Rest\ListResource\ServiceListener::class                => [
                    'type'    => Literal::class,
                    'options' => [
                        'route'    => '/api/list/service',
                        'defaults' => [
                            'controller' => Rest\ListResource\ServiceListener::class,
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
                Rest\ListResource\Project\VersionListener::class        => [
                    'type'    => Literal::class,
                    'options' => [
                        'route'    => '/api/list/project/version',
                        'defaults' => [
                            'controller' => Rest\ListResource\Project\VersionListener::class,
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
                ResultListener::class                                   => [
                    'type'    => Literal::class,
                    'options' => [
                        'route'    => '/api/search/result',
                        'defaults' => [
                            'controller' => ResultListener::class,
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
                        'route'    => '/api/statistics/facets/project/[:filter]',
                        'defaults' => [
                            'controller' => Rest\StatisticsResource\Facets\ProjectListener::class,
                        ],
                    ],
                ],
                Rest\StatisticsResource\Facets\PartnerListener::class   => [
                    'type'    => Segment::class,
                    'options' => [
                        'route'    => '/api/statistics/facets/partner/[:filter]',
                        'defaults' => [
                            'controller' => Rest\StatisticsResource\Facets\PartnerListener::class,
                        ],
                    ],
                ],
                Rest\StatisticsResource\Download\ProjectListener::class => [
                    'type'    => Segment::class,
                    'options' => [
                        'route'    => '/api/statistics/results/project/download/[:filter]',
                        'defaults' => [
                            'controller' => Rest\StatisticsResource\Download\ProjectListener::class,
                        ],
                    ],
                ],
                Rest\StatisticsResource\Download\PartnerListener::class => [
                    'type'    => Segment::class,
                    'options' => [
                        'route'    => '/api/statistics/results/partner/download/[:filter]',
                        'defaults' => [
                            'controller' => Rest\StatisticsResource\Download\PartnerListener::class,
                        ],
                    ],
                ],
            ],
        ];
    }
}
