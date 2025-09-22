<?php

declare(strict_types=1);

namespace Api;

use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use OpenApi\Attributes as OA;
use Override;

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
final class Module implements ConfigProviderInterface
{
    #[Override]
    public function getConfig(): array
    {
        $aggregator = new ConfigAggregator([
                                               ConfigProvider::class,
                                           ]);
        return $aggregator->getMergedConfig();
    }
}
