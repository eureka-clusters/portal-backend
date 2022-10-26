<?php

declare(strict_types=1);

namespace DeeplinkTest;

use Deeplink\Module;
use Laminas\Router\Http\TreeRouteStack;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\View\HelperPluginManager;
use Testing\Util\AbstractServiceTest;

use function str_contains;

class ModuleTest extends AbstractServiceTest
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

            if (str_contains((string) $service, 'Filter')) {
                continue;
            }

            foreach ($dependencies as $dependency) {
                if ($dependency === 'ViewHelperManager') {
                    $dependency = HelperPluginManager::class;
                }
                if ($dependency === 'Router') {
                    $dependency = TreeRouteStack::class;
                }
                $instantiatedDependencies[]
                    = $this->getMockBuilder($dependency)->disableOriginalConstructor()->getMock();
            }

            $instance = new $service(...$instantiatedDependencies);

            self::assertInstanceOf($service, $instance);
        }
    }
}
