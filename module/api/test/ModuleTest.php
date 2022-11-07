<?php

declare(strict_types=1);

namespace ApiTest;

use Api\Module;
use Laminas\Mvc\Application;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\View\HelperPluginManager;
use Testing\Util\AbstractServiceTest;

use function is_string;

class ModuleTest extends AbstractServiceTest
{
    public function testCanFindConfiguration(): void
    {
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
            //Skip the Filters
            $instantiatedDependencies = [];
            foreach ($dependencies as $dependency) {
                if ($dependency === 'Application') {
                    $dependency = Application::class;
                }
                if ($dependency === 'ViewHelperManager') {
                    $dependency = HelperPluginManager::class;
                }
                if ($dependency === 'Config') {
                    $dependency = [];
                }

                if (is_string(value: $dependency)) {
                    $instantiatedDependencies[] = $this->getMockBuilder(
                        className: $dependency
                    )->disableOriginalConstructor()
                        ->getMock();
                } else {
                    $instantiatedDependencies[] = [];
                }
            }

            $instance = new $service(...$instantiatedDependencies);

            self::assertInstanceOf(expected: $service, actual: $instance);
        }
    }
}
