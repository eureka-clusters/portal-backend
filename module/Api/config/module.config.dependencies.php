<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Api;

use Admin\Provider\UserProvider;
use Admin\Service\UserService;
use Api\Options\ModuleOptions;
use Api\V1\Rest;
use Cluster\Provider\OrganisationProvider;
use Cluster\Provider\Project\PartnerProvider;
use Cluster\Provider\ProjectProvider;
use Cluster\Service\OrganisationService;
use Cluster\Service\Project\PartnerService;
use Cluster\Service\Project\VersionService;
use Cluster\Service\ProjectService;
use Doctrine\ORM\EntityManager;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        Rest\UserResource\MeListener::class                     => [
            UserService::class,
            UserProvider::class
        ],
        Rest\ListResource\OrganisationListener::class           => [
            OrganisationService::class,
            OrganisationProvider::class
        ],
        Rest\ListResource\ProjectListener::class                => [
            ProjectService::class,
            UserService::class,
            ProjectProvider::class
        ],
        Rest\ListResource\PartnerListener::class                => [
            PartnerService::class,
            ProjectService::class,
            OrganisationService::class,
            UserService::class,
            PartnerProvider::class
        ],
        Rest\ViewResource\ProjectListener::class                => [
            ProjectService::class,
            ProjectProvider::class
        ],
        Rest\ViewResource\OrganisationListener::class           => [
            OrganisationService::class,
            OrganisationProvider::class
        ],
        Rest\ViewResource\PartnerListener::class                => [
            PartnerService::class,
            PartnerProvider::class
        ],
        Rest\UpdateResource\ProjectListener::class              => [
            ProjectService::class,
            VersionService::class,
            PartnerService::class,
            EntityManager::class
        ],
        Rest\StatisticsResource\Facets\ProjectListener::class   => [
            ProjectService::class,
            UserService::class,
        ],
        Rest\StatisticsResource\Facets\PartnerListener::class   => [
            PartnerService::class,
            UserService::class
        ],
        Rest\StatisticsResource\Results\ProjectListener::class  => [
            ProjectService::class,
            UserService::class,
            ProjectProvider::class
        ],
        Rest\StatisticsResource\Results\PartnerListener::class  => [
            PartnerService::class,
            UserService::class,
            PartnerProvider::class
        ],
        Rest\StatisticsResource\Download\ProjectListener::class => [
            ProjectService::class,
            UserService::class,
            TranslatorInterface::class,
            ProjectProvider::class
        ],
        Rest\StatisticsResource\Download\PartnerListener::class => [
            PartnerService::class,
            UserService::class,
            TranslatorInterface::class,
            PartnerProvider::class
        ],
        Service\OAuthService::class                             => [
            EntityManager::class,
            TranslatorInterface::class,
            ModuleOptions::class
        ],
    ]
];
