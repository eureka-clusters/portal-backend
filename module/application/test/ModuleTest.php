<?php

declare(strict_types=1);

namespace ApplicationTest;

use Application\Module;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Testing\Util\AbstractServiceTest;

final class ModuleTest extends AbstractServiceTest
{
    public function testCanFindConfiguration(): void
    {
        $module = new Module();
        $config = $module->getConfig();

        self::assertIsArray(actual: $config);
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
                $instantiatedDependencies[]
                    = $this->getMockBuilder(className: $dependency)->disableOriginalConstructor()->getMock();
            }

            $instance = new $service(...$instantiatedDependencies);

            self::assertInstanceOf(expected: $service, actual: $instance);
        }
    }
}
