<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Admin;

use Admin\Form\View\Helper\SelectionFormElement;
use Admin\Form\View\Helper\UserFormElement;
use Admin\OAuth2\TokenProvider;
use Admin\Service\AdminService;
use Admin\Service\QueueService;
use Admin\Service\SelectionService;
use Admin\Service\SelectionUserService;
use Admin\Service\UserService;
use Application\Service\FormService;
use Deeplink\Service\DeeplinkService;
use Doctrine\Common\Cache\RedisCache;
use Doctrine\ORM\EntityManager;
use Laminas\Authentication\AuthenticationService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use League\OAuth2\Client\Provider\GenericProvider;
use Mailing\Service\EmailService;

return [
    ConfigAbstractFactory::class => [
        Service\UserService::class                 => [
            EntityManager::class,
            AdminService::class,
            'ControllerPluginManager'
        ],
        Service\QueueService::class                => [
            EntityManager::class
        ],
        Service\ApiService::class                  => [
            EntityManager::class
        ],

    ]
];
