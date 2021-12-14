<?php

declare(strict_types=1);

namespace Api;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ApiTools\Provider\ApiToolsProviderInterface;
use Laminas\ModuleManager\Feature;

final class Module implements ApiToolsProviderInterface, ConfigProviderInterface
{
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/api.config.php';
    }
}
