<?php

declare(strict_types=1);

namespace Api;

use Laminas\ApiTools\Provider\ApiToolsProviderInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;

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
