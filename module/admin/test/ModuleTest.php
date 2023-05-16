<?php

declare(strict_types=1);

namespace AdminTest;

use Admin\Module;
use Laminas\Mvc\Controller\PluginManager;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Testing\Util\AbstractServiceTest;
use DG\BypassFinals;

class ModuleTest extends AbstractServiceTest
{
    public function testCanFindConfiguration(): void
    {
        BypassFinals::enable();

        $module = new Module();
        $config = $module->getConfig();

        self::assertArrayHasKey(key: 'service_manager', array: $config);
        self::assertArrayHasKey(key: ConfigAbstractFactory::class, array: $config);
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
                        className: PluginManager::class
                    )->disableOriginalConstructor()->getMock();
                } elseif ($dependency === 'ViewHelperManager') {
                    $instantiatedDependencies[] = $this->getMockBuilder(
                        className: \Laminas\View\Helper\Navigation\PluginManager::class
                    )->disableOriginalConstructor()->getMock();
                } else {
                    $instantiatedDependencies[]
                        = $this->getMockBuilder(className: $dependency)->disableOriginalConstructor()->getMock();
                }
            }

            $instance = new $service(...$instantiatedDependencies);

            self::assertInstanceOf(expected: $service, actual: $instance);
        }
    }
}
