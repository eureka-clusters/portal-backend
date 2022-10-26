<?php

declare(strict_types=1);

namespace MailingTest;

use Laminas\Mvc\Application;
use Laminas\Mvc\Controller\PluginManager;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\View\HelperPluginManager;
use Mailing\Module;
use Testing\Util\AbstractServiceTest;

use function str_contains;

class ModuleTest extends AbstractServiceTest
{
    public function testCanFindConfiguration(): void
    {
        $module = new Module();
        $config = $module->getConfig();

        $this->assertArrayHasKey('service_manager', $config);
        $this->assertArrayHasKey(ConfigAbstractFactory::class, $config);
    }

    public function testInstantiationOfConfigAbstractFactories(): void
    {
        $module = new Module();
        $config = $module->getConfig();

        $abstractFactories = $config[ConfigAbstractFactory::class] ?? [];

        foreach ($abstractFactories as $service => $dependencies) {
            if (str_contains((string) $service, 'Filter')) {
                continue;
            }

            $instantiatedDependencies = [];
            foreach ($dependencies as $dependency) {
                if ($dependency === 'Application') {
                    $dependency = Application::class;
                }
                if ($dependency === 'ViewHelperManager') {
                    $dependency = HelperPluginManager::class;
                }

                if ($dependency === 'ViewHelperManager') {
                    $dependency = HelperPluginManager::class;
                }

                if ($dependency === 'ControllerPluginManager') {
                    $dependency = PluginManager::class;
                }

                $instantiateDependency = $this->getMockBuilder($dependency)->disableOriginalConstructor()->getMock();

                $instantiatedDependencies[] = $instantiateDependency;
            }

            $instance = new $service(...$instantiatedDependencies);

            $this->assertInstanceOf($service, $instance);
        }
    }
}
