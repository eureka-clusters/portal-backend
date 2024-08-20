<?php

declare(strict_types=1);

namespace Api\V1\Rest\ListResource;

use Admin\Service\oAuth2Service;
use Api\Entity\OAuth\Service;
use Api\Listener\AbstractRoutedListener;
use Api\Provider\OAuth\ServiceProvider;
use OpenApi\Attributes as OA;
use function array_map;

final class ServiceListener extends AbstractRoutedListener
{
    protected static string $route = '/api/list/service';

    public function __construct(
        private readonly oAuth2Service   $oAuth2Service,
        private readonly ServiceProvider $serviceProvider
    )
    {
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
    #[\Override]
    public function fetchAll($params = [])
    {
        return array_map(
            fn(Service $service) => $this->serviceProvider->generateArray($service),
            $this->oAuth2Service->findAllService()
        );
    }
}
