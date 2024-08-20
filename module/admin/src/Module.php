<?php

declare(strict_types=1);

namespace Admin;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    #[\Override]
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
