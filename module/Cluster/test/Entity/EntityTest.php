<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

namespace ClusterTest\Entity;

use Application\Entity\AbstractEntity;
use Laminas\Form\Annotation\AnnotationBuilder;
use Laminas\Form\Element;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

use function file_put_contents;
use function is_array;
use function str_replace;

final class EntityTest extends TestCase
{
    public function testCanCreateEntitiesAndSaveTxtFields(): void
    {
        $labels = [];

        $scanFolder = __DIR__ . '/../../src/Entity';

        $finder = new Finder();
        $finder->files()->name('*.php')->in($scanFolder);

        foreach ($finder as $file) {
            $className = 'Admin\Entity\\' . str_replace(['/', '.php'], ['\\', ''], $file->getRelativePathname());

            $testClass = new ReflectionClass($className);

            if ($testClass->isInstantiable()) {
                /** @var AbstractEntity $object */
                $object = new $className();

                self::assertInstanceOf($className, $object);

                $builder      = new AnnotationBuilder();
                $dataFieldset = $builder->createForm($object);

                /** @var Element $element */
                foreach ($dataFieldset->getElements() as $element) {
                    // Add only when a type is provided
                    if (!array_key_exists('type', $element->getAttributes())) {
                        continue;
                    }

                    if (isset($element->getAttributes()['label'])) {
                        $labels[] = $element->getAttributes()['label'];
                    }
                    if (isset($element->getAttributes()['help-block'])) {
                        $labels[] = $element->getAttributes()['help-block'];
                    }
                    if (isset($element->getAttributes()['placeholder'])) {
                        $labels[] = $element->getAttributes()['placeholder'];
                    }
                    if (isset($element->getOptions()['label'])) {
                        $labels[] = $element->getOptions()['label'];
                    }
                    if (isset($element->getOptions()['help-block'])) {
                        $labels[] = $element->getOptions()['help-block'];
                    }
                    if (isset($element->getOptions()['placeholder'])) {
                        $labels[] = $element->getOptions()['placeholder'];
                    }

                    self::assertIsArray($element->getAttributes());
                    self::assertIsArray($element->getOptions());
                }

                foreach ($testClass->getStaticProperties() as $constant) {
                    if (is_array($constant)) {
                        foreach ($constant as $constantValue) {
                            $labels[] = $constantValue;
                        }
                    }
                }
            }
        }

        file_put_contents(
            __DIR__ . '/../../config/language.php',
            "<?php\n\n_('" . implode("');\n_('", array_unique($labels)) . "');\n"
        );
    }
}
