<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Admin;

use Application\Version\Version;
use Laminas\Console\Adapter\AdapterInterface;
use Laminas\ModuleManager\Feature;

/**
 * Class Module
 * @package Admin
 */
class Module implements
    Feature\ConfigProviderInterface,
    Feature\ConsoleUsageProviderInterface,
    Feature\ConsoleBannerProviderInterface
{
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }


    public function getConsoleUsage(AdapterInterface $console): array
    {
        return [
            'User management',
            // Describe available commands
            'admin permit flush-all' => 'FLush all privileges in the database'
        ];
    }

    public function getConsoleBanner(AdapterInterface $console): string
    {
        return 'Safety Form ' . Version::VERSION . ' console application - powered by laminas Framework 3';
    }
}
