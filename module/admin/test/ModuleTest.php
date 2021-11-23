<?php

declare(strict_types=1);

namespace AdminTest;

use Admin\Module;
use Laminas\Mvc\Controller\PluginManager;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Testing\Util\AbstractServiceTest;

class ModuleTest extends AbstractServiceTest
{
    public function testCanFindConfiguration(): void
    {
        $module = new Module();
        $config = $module->getConfig();

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
                    $instantiatedDependencies[] = [];
                } elseif ($dependency === 'ControllerPluginManager') {
                    $instantiatedDependencies[] = $this->getMockBuilder(
                        PluginManager::class
                    )->disableOriginalConstructor()->getMock();
                } elseif ($dependency === 'ViewHelperManager') {
                    $instantiatedDependencies[] = $this->getMockBuilder(
                        \Laminas\View\Helper\Navigation\PluginManager::class
                    )->disableOriginalConstructor()->getMock();
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
