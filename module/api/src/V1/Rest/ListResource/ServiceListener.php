<?php

declare(strict_types=1);

namespace Api\V1\Rest\ListResource;

use Admin\Service\OAuth2Service;
use Api\Entity\OAuth\Service;
use Api\Provider\OAuth\ServiceProvider;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use OpenApi\Attributes as OA;

use function array_map;

final class ServiceListener extends AbstractResourceListener
{
    public function __construct(
        private readonly OAuth2Service $OAuth2Service,
        private readonly ServiceProvider $serviceProvider
    ) {
    }

    #[OA\Get(
        path: '/api/list/service',
        description: 'oAuth2 services information',
        summary: 'Get a list of all oAuth2 services',
        tags: ['User'],
        responses: [
            new OA\Response(ref: '#/components/responses/service', response: 200),
        ],
    )]
    public function fetchAll($params = [])
    {
        return array_map(
            fn (Service $service) => $this->serviceProvider->generateArray($service),
            $this->OAuth2Service->findAllService()
        );
    }
}
