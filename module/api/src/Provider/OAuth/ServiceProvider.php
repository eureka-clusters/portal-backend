<?php

declare(strict_types=1);

namespace Api\Provider\OAuth;

use Api\Entity\OAuth\Service;
use Api\Provider\ProviderInterface;
use Application\Options\ModuleOptions;
use Laminas\View\Helper\Url;
use Laminas\View\HelperPluginManager;

use function array_merge;

final class ServiceProvider implements ProviderInterface
{
    public function __construct(
        private readonly HelperPluginManager $helperPluginManager,
        private readonly ModuleOptions $moduleOptions
    ) {
    }

    public function generateArray($entity): array
    {
        /** @var Service $service */
        $service = $entity;

        /** @var Url $urlHelper */
        $urlHelper = $this->helperPluginManager->get(name: 'url');

        return array_merge(
            [
                'id' => $service->getId(),
                'name' => $service->getName(),
                'loginUrl' => sprintf(
                    '%s%s',
                    $this->moduleOptions->getServerUrl(),
                    $urlHelper(name: 'oauth2/login', params: [
                        'id' => $service->getId(),
                        'name' => $service->getName()
                    ])
                ),
            ]
        );
    }
}
