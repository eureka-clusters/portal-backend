<?php

declare(strict_types=1);

namespace Api\V1\Rest\ListResource;

use Admin\Service\OAuth2Service;
use Api\Entity\OAuth\Service;
use Api\Provider\OAuth\ServiceProvider;
use Laminas\ApiTools\Rest\AbstractResourceListener;

use function array_map;

final class ServiceListener extends AbstractResourceListener
{
    public function __construct(
        private readonly OAuth2Service $OAuth2Service,
        private readonly ServiceProvider $serviceProvider
    ) {
    }

    public function fetchAll($params = [])
    {
        return array_map(
            fn (Service $service) => $this->serviceProvider->generateArray($service),
            $this->OAuth2Service->findAllService()
        );
    }
}
