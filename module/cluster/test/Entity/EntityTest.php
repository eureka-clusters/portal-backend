<?php

declare(strict_types=1);

namespace ClusterTest\Entity;

use ApplicationTest\Entity\AbstractEntityTest;

class EntityTest extends AbstractEntityTest
{
    private string $namespace = 'cluster';

    public function testCorrectPropertiesOfEntities(): void
    {
        $entities = $this->getEntities(namespace: $this->namespace, baseFolder: __DIR__);

        foreach ($entities as $className => $reflectionClass) {
            $this->analyseClass($reflectionClass);

            foreach ($reflectionClass->getProperties() as $property) {
                $this->analyseClassProperty(new $className(), $property);
            }
        }
    }

    public function testToString(): void
    {
        $entities = $this->getEntities(namespace: $this->namespace, baseFolder: __DIR__);

        foreach ($entities as $className => $reflectionClass) {
            $class = new $className();
            $class->setId(1);
            self::assertIsString((string)$class);
        }
    }
}
