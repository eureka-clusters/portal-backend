<?php

declare(strict_types=1);

namespace Api;

use Admin\Entity\Role;
use Api\Provider\OAuth\ServiceProvider;
use Api\V1\Rest;
use Application\Options\ModuleOptions;
use BjyAuthorize\Guard\Route;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Rector\Symfony\DataProvider\ServiceMapProvider;

final class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            ConfigAbstractFactory::class => [
                ServiceProvider::class => [
                    'ViewHelperManager',
                    ModuleOptions::class
                ],
            ],
            'service_manager'            => [
                'factories' => [
                    ServiceProvider::class => ConfigAbstractFactory::class,
                ],
            ],
            'doctrine'                   => $this->getDoctrineConfig(),
            'bjyauthorize'               => [
                'guards' => $this->getRouteGuardConfig()
            ]
        ];
    }

    private function getDoctrineConfig(): array
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

    private function getRouteGuardConfig(): array
    {
        return [
            Route::class => [
                ['route' => 'oauth', 'roles' => []],
                ['route' => 'oauth/authorize', 'roles' => [Role::ROLE_USER]],
                ['route' => 'oauth/resource', 'roles' => [Role::ROLE_USER]],
                ['route' => 'oauth/code', 'roles' => [Role::ROLE_USER]],
            ],
        ];
    }
}
