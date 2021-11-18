<?php

declare(strict_types=1);

namespace Application;

use Laminas\ModuleManager\Feature;

class Module implements Feature\ConfigProviderInterface
{
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
