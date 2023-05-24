<?php

declare(strict_types=1);

namespace Cluster;

use Application\Factory\InputFilterFactory;
use Application\Factory\InvokableFactory;
use Application\View\Factory\LinkHelperFactory;
use Cluster\Controller\Cluster\GroupController;
use Cluster\Controller\ProjectController;
use Cluster\InputFilter\Cluster\GroupFilter;
use Cluster\Navigation\Invokable\Cluster\GroupLabel;
use Cluster\Navigation\Invokable\ProjectLabel;
use Cluster\Provider\ClusterProvider;
use Cluster\Provider\ContactProvider;
use Cluster\Provider\CountryProvider;
use Cluster\Provider\Organisation\TypeProvider;
use Cluster\Provider\OrganisationProvider;
use Cluster\Provider\Project\Partner\CoordinatorProvider;
use Cluster\Provider\Project\PartnerProvider;
use Cluster\Provider\Project\PartnerYearProvider;
use Cluster\Provider\Project\StatusProvider;
use Cluster\Provider\Project\VersionProvider;
use Cluster\Provider\ProjectProvider;
use Cluster\Provider\SearchResultProvider;
use Cluster\Service\ClusterService;
use Cluster\Service\CountryService;
use Cluster\Service\OrganisationService;
use Cluster\Service\Project\PartnerService;
use Cluster\Service\Project\VersionService;
use Cluster\Service\ProjectService;
use Cluster\View\Helper\Cluster\GroupLink;
use Cluster\View\Helper\ProjectLink;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\Glob;


$config = [
    'controllers'     => [
        'factories' => [
            ProjectController::class => ConfigAbstractFactory::class,
            GroupController::class   => ConfigAbstractFactory::class,
        ]
    ],
    'service_manager' => [
        'factories'  => [
            ClusterProvider::class                 => ConfigAbstractFactory::class,
            OrganisationProvider::class            => ConfigAbstractFactory::class,
            TypeProvider::class                    => ConfigAbstractFactory::class,
            ProjectProvider::class                 => ConfigAbstractFactory::class,
            PartnerProvider::class                 => ConfigAbstractFactory::class,
            CoordinatorProvider::class             => ConfigAbstractFactory::class,
            PartnerYearProvider::class             => ConfigAbstractFactory::class,
            StatusProvider::class                  => ConfigAbstractFactory::class,
            VersionProvider::class                 => ConfigAbstractFactory::class,
            Provider\Version\StatusProvider::class => ConfigAbstractFactory::class,
            Provider\Version\TypeProvider::class   => ConfigAbstractFactory::class,
            CountryProvider::class                 => ConfigAbstractFactory::class,
            ClusterService::class                  => ConfigAbstractFactory::class,
            CountryService::class                  => ConfigAbstractFactory::class,
            OrganisationService::class             => ConfigAbstractFactory::class,
            ProjectService::class                  => ConfigAbstractFactory::class,
            VersionService::class                  => ConfigAbstractFactory::class,
            PartnerService::class                  => ConfigAbstractFactory::class,
            GroupFilter::class                     => InputFilterFactory::class,
            GroupLabel::class                      => InvokableFactory::class,
            ProjectLabel::class                    => InvokableFactory::class,
        ],
        'invokables' => [
            SearchResultProvider::class,
            ContactProvider::class
        ],
    ],
    'view_helpers'    => [
        'aliases'   => [
            'clusterGroupLink' => GroupLink::class,
            'projectLink'      => ProjectLink::class,
        ],
        'factories' => [
            GroupLink::class   => LinkHelperFactory::class,
            ProjectLink::class => LinkHelperFactory::class,
        ],
    ],
    'view_manager'    => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'doctrine'        => [
        'driver' => [
            'cluster_attribute_driver' => [
                'class' => AttributeDriver::class,
                'paths' => [__DIR__ . '/../src/Entity/'],
            ],
            'orm_default'              => [
                'drivers' => [
                    'Cluster\Entity' => 'cluster_attribute_driver',
                ],
            ],
        ],
    ],
];
foreach (Glob::glob(pattern: __DIR__ . '/module.config.{,*}.php', flags: Glob::GLOB_BRACE) as $file) {
    $config = ArrayUtils::merge(a: $config, b: include $file);
}
return $config;
