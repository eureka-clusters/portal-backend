<?php

declare(strict_types=1);

namespace Api\Provider\OAuth;

use Api\Entity\OAuth\Service;
use Api\Provider\ProviderInterface;
use Application\Options\ModuleOptions;
use Laminas\View\Helper\Url;
use Laminas\View\HelperPluginManager;
use OpenApi\Attributes as OA;

use function array_merge;
use function sprintf;

#[OA\Response(
    response: 'service',
    description: 'oAuth2 service information',
    content: new OA\JsonContent(ref: '#/components/schemas/service'),
)]
final class ServiceProvider implements ProviderInterface
{
    public function __construct(
        private readonly HelperPluginManager $helperPluginManager,
        private readonly ModuleOptions $moduleOptions
    ) {
    }

    #[OA\Schema(
        schema: 'service',
        title: 'oAuth2 service',
        description: 'oAuth2 service information',
        properties: [
            new OA\Property(
                property: 'id',
                description: 'oAuth2 service ID',
                type: 'integer',
                example: 1
            ),
            new OA\Property(
                property: 'name',
                description: 'oAuth2 service name',
                type: 'string',
                example: 'ITEA Office'
            ),
            new OA\Property(
                property: 'loginUrl',
                description: 'oAuth2 service login URL',
                type: 'string',
                example: 'https://itea4.org/oauth2/login?id=1&name=portal'
            ),
        ],
    )]
    public function generateArray($entity): array
    {
        /** @var Service $service */
        $service = $entity;

        /** @var Url $urlHelper */
        $urlHelper = $this->helperPluginManager->get(name: 'url');

        return array_merge(
            [
                'id'       => $service->getId(),
                'name'     => $service->getName(),
                'loginUrl' => sprintf(
                    '%s%s',
                    $this->moduleOptions->getServerUrl(),
                    $urlHelper(name: 'oauth2/login', params: [
                        'id'   => $service->getId(),
                        'name' => $service->getName(),
                    ])
                ),
            ]
        );
    }
}
