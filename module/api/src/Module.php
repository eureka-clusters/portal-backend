<?php

declare(strict_types=1);

namespace Api;

use Laminas\ApiTools\Provider\ApiToolsProviderInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use OpenApi\Attributes as OA;

#[OA\Info(version: '1.0', title: 'Eureka Clusters backend API')]
#[OA\ExternalDocumentation(description: 'Backend API code. Find more information in the official documentation',
    url: 'https://eureka-clusters.github.io/portal-backend/')]
#[OA\OpenApi(
    security: [
        [
            'bearerAuth' => []
        ]
    ],
),
]
#[OA\SecurityScheme(securityScheme: 'bearerAuth', type: 'http', bearerFormat: 'JWT', scheme: 'bearer')]
#[OA\Tag(name: 'Project', description: 'Project related endpoints')]
#[OA\Tag(name: 'Organisation', description: 'Organisation related endpoints')]
#[OA\Tag(name: 'User', description: 'User related endpoints')]
final class Module implements ApiToolsProviderInterface, ConfigProviderInterface
{
    public function getConfig(): array
    {
        $configProvider    = new ConfigProvider();
        $apiConfigProvider = new ApiConfigProvider();

        return [
            ConfigAbstractFactory::class   => $configProvider->getConfigAbstractFactory(),
            'service_manager'              => $configProvider->getDependencyConfig(),
            'doctrine'                     => $configProvider->getDoctrineConfig(),
            'router'                       => $configProvider->getRouteConfig(),
            'bjyauthorize'                 => $configProvider->getGuardConfig(),
            'api-tools-rest'               => $apiConfigProvider->getApiToolsRestConfig(),
            'api-tools-mvc-auth'           => $apiConfigProvider->getApiToolsMvcConfig(),
            'api-tools-content-validation' => $apiConfigProvider->getApiToolsContentValidationConfig(),
            'input_filter_specs'           => $apiConfigProvider->getApiToolsInputFilterSpecsConfig(),
        ];
    }
}
