<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace ClusterTest;

use Cluster\Module;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Testing\Util\AbstractServiceTest;

/**
 * Class ModuleTest
 * @package ClusterTest
 */
final class ModuleTest extends AbstractServiceTest
{
    public function testCanFindConfiguration(): void
    {
        $module = new Module();
        $config = $module->getConfig();

        self::assertIsArray($config);
        self::assertArrayHasKey('service_manager', $config);
        self::assertArrayHasKey(ConfigAbstractFactory::class, $config);
    }

    public function testInstantiationOfConfigAbstractFactories(): void
    {
        $module = new Module();
        $config = $module->getConfig();

        $abstractFactories = $config[ConfigAbstractFactory::class] ?? [];

        foreach ($abstractFactories as $service => $dependencies) {
            $instantiatedDependencies = [];
            foreach ($dependencies as $dependency) {
                if ($dependency === 'Config') {
                    $instantiatedDependencies[] = [
                        'youtrack' => [
                            'url'      => '',
                            'username' => '',
                            'program'  => ''
                        ]
                    ];
                } else {
                    $instantiatedDependencies[]
                        = $this->getMockBuilder($dependency)->disableOriginalConstructor()->getMock();
                }
            }

            $instance = new $service(...$instantiatedDependencies);

            self::assertInstanceOf($service, $instance);
        }
    }
}
