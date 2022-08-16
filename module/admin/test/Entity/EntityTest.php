<?php

declare(strict_types=1);

namespace AdminTest\Entity;

use ApplicationTest\Entity\AbstractEntityTest;

class EntityTest extends AbstractEntityTest
{
    private string $namespace = 'admin';

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

    public function testCanCreateEntitiesAndSaveTxtFields(): void
    {
        $this->canCreateEntitiesAndSaveTxtFields(
            namespace: $this->namespace,
            baseFolder: __DIR__,
        );
    }
}
