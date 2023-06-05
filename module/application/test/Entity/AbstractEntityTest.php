<?php

declare(strict_types=1);

namespace ApplicationTest\Entity;

use Application\Entity\AbstractEntity;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Driver\AttributeReader;
use Doctrine\ORM\Mapping\Driver\RepeatableAttributeCollection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;
use Laminas\Form\Annotation\AttributeBuilder;
use Laminas\Form\Element;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

use function array_key_exists;
use function array_unique;
use function class_exists;
use function file_put_contents;
use function implode;
use function is_array;
use function sprintf;
use function str_replace;
use function ucfirst;

abstract class AbstractEntityTest extends TestCase
{
    public function canCreateEntitiesAndSaveTxtFields(string $namespace, string $baseFolder): void
    {
        $entities = $this->getEntities(namespace: $namespace, baseFolder: $baseFolder);

        $labels = [];
        foreach ($entities as $className => $reflectionClass) {
            $builder      = new AttributeBuilder();
            $dataFieldset = $builder->createForm(entity: new $className());

            /** @var Element $element */
            foreach ($dataFieldset->getElements() as $element) {
                // Add only when a type is provided
                if (!array_key_exists(key: 'type', array: $element->getAttributes())) {
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

                $this->assertIsArray(actual: $element->getAttributes());
                $this->assertIsArray(actual: $element->getOptions());
            }

            foreach ($reflectionClass->getStaticProperties() as $constant) {
                if (is_array(value: $constant)) {
                    foreach ($constant as $constantValue) {
                        $labels[] = $constantValue;
                    }
                }
            }
        }

        file_put_contents(
            filename: $baseFolder . '/../../config/language.php',
            data: "<?php\n\ndeclare(strict_types=1);\n\n_('" . implode(
                separator: "');\n_('",
                array: array_unique(
                    array: $labels
                )
            ) . "');\n"
        );
    }

    protected function getEntities(string $namespace, string $baseFolder): array
    {
        $scanFolder = $baseFolder . '/../../src/Entity';

        $finder = new Finder();
        $finder->files()->name(patterns: '*.php')->in(dirs: $scanFolder);

        $entities = [];

        foreach ($finder as $fileInfo) {
            $reflectionClass = $this->getReflectionClassFromFileInfo(namespace: $namespace, fileInfo: $fileInfo);

            if ($reflectionClass->isInstantiable()) {
                $className            = $this->getClassNameFromFileInfo(namespace: $namespace, fileInfo: $fileInfo);
                $entities[$className] = $reflectionClass;
            }
        }

        return $entities;
    }

    protected function getReflectionClassFromFileInfo(string $namespace, SplFileInfo $fileInfo): ReflectionClass
    {
        return new ReflectionClass(
            objectOrClass: $this->getClassNameFromFileInfo(
                namespace: $namespace,
                fileInfo: $fileInfo
            )
        );
    }

    protected function getClassNameFromFileInfo(string $namespace, SplFileInfo $fileInfo): string
    {
        return ucfirst(string: $namespace) . '\Entity\\' . str_replace(
                search: ['/', '.php'],
                replace: ['\\', ''],
                /** @phpstan-ignore-next-line */
                subject: $fileInfo->getRelativePathname()
            );
    }

    protected function analyseClass(ReflectionClass $class): void
    {
        $builder          = new AttributeReader();
        $classAnnotations = $builder->getClassAttributes(class: $class);

        self::assertArrayHasKey(
            key: Table::class,
            array: $classAnnotations,
            message: sprintf(
            '%s should have a table',
            $class->getName()
        )
        );
        self::assertArrayHasKey(
            key: Entity::class,
            array: $classAnnotations,
            message: sprintf('%s should have an entity', $class->getName())
        );

        //Get the entity annotation
        /** @var Entity $entityAnnotation */
        $entityAnnotation = $classAnnotations[Entity::class];
        if (null !== $entityAnnotation->repositoryClass) {
            self::assertTrue(
                condition: class_exists(class: $entityAnnotation->repositoryClass),
                message: sprintf(
                    'Repository class %s cannot be found for %s',
                    $entityAnnotation->repositoryClass,
                    $class->getName()
                )
            );
        }
    }

    protected function analyseClassProperty(AbstractEntity $entity, ReflectionProperty $property): void
    {
        $builder = new AttributeReader();
        //Try to match the doctrine entities and the class proprety
        $propertyAnnotations = $builder->getPropertyAttributes(property: $property);

        $propertyName = $property->getName();

        //produce the getters and setters
        $setter = 'set' . ucfirst(string: $propertyName);
        $getter = 'get' . ucfirst(string: $propertyName);

        switch (true) {
            case array_key_exists(key: Column::class, array: $propertyAnnotations):
                $this->analyseColumnMapping(
                    entity: $entity,
                    propertyName: $propertyName,
                    getter: $getter,
                    setter: $setter,
                    annotationProperties: $propertyAnnotations
                );
                break;
            case array_key_exists(key: OneToMany::class, array: $propertyAnnotations):
                $this->analyseOneToManyMapping(
                    entity: $entity,
                    propertyName: $propertyName,
                    getter: $getter,
                    setter: $setter,
                    propertyAnnotations: $propertyAnnotations
                );
                break;
            case array_key_exists(key: OneToOne::class, array: $propertyAnnotations):
                $this->analyseOneToOneMapping(
                    entity: $entity,
                    propertyName: $propertyName,
                    getter: $getter,
                    setter: $setter,
                    propertyAnnotations: $propertyAnnotations
                );
                break;
            case array_key_exists(key: ManyToOne::class, array: $propertyAnnotations):
                $this->analyseManyToOneMapping(
                    entity: $entity,
                    propertyName: $propertyName,
                    getter: $getter,
                    setter: $setter,
                    propertyAnnotations: $propertyAnnotations
                );
                break;
            case array_key_exists(key: ManyToMany::class, array: $propertyAnnotations):
                $this->analyseManyToManyMapping(
                    entity: $entity,
                    propertyName: $propertyName,
                    getter: $getter,
                    setter: $setter,
                    propertyAnnotations: $propertyAnnotations
                );
                break;
        }
    }

    protected function analyseColumnMapping(
        AbstractEntity $entity,
        string $propertyName,
        string $getter,
        string $setter,
        array $annotationProperties
    ): void {
        //Go over the properties and do the required tests
        $columnAnnotation = $annotationProperties[Column::class];

        if ($columnAnnotation->nullable) {
            $entity->$setter(null);
            self::assertNull(actual: $entity->$getter());
        }

        if (!$columnAnnotation->nullable) {
            switch ($columnAnnotation->type) {
                case 'integer':
                case 'smallint':
                    if ($getter !== 'getId') {
                        self::assertNotNull(
                            actual: $entity->$getter(),
                            message: sprintf('%s on %s should not be null', $getter, $entity::class)
                        );
                    }
                    $entity->$setter(1);
                    self::assertEquals(expected: 1, actual: $entity->$getter());
                    break;
                case 'datetime':
                    $new = new DateTime();
                    self::assertNotNull(
                        actual: $entity->$getter(),
                        message: sprintf('%s on %s should not be null', $getter, $entity::class)
                    );
                    $entity->$setter($new);
                    self::assertEquals(expected: $new, actual: $entity->$getter());
                    break;
                case 'string':
                case 'text':
                    self::assertNotNull(
                        actual: $entity->$getter(),
                        message: sprintf('%s on %s should not be null', $getter, $entity::class)
                    );
                    $entity->$setter('this is a string');
                    self::assertEquals(expected: 'this is a string', actual: $entity->$getter());
                    break;
            }
        }
    }

    protected function analyseOneToManyMapping(
        AbstractEntity $entity,
        string $propertyName,
        string $getter,
        string $setter,
        array $propertyAnnotations
    ): void {
        $oneToManyAnnotation = $propertyAnnotations[OneToMany::class];

        //Do something

        //The initial value should be an arrayCollection
        self::assertInstanceOf(expected: ArrayCollection::class, actual: $entity->$getter());

        //And we need to be able to set a collection
        $collection = new ArrayCollection();
        $entity->$setter($collection);
        $this->assertEquals(expected: $collection, actual: $entity->$getter());
    }

    protected function analyseOneToOneMapping(
        AbstractEntity $entity,
        string $propertyName,
        string $getter,
        string $setter,
        array $propertyAnnotations
    ): void {
        /** @var OneToOne $oneToOneAnnotation */
        $oneToOneAnnotation = $propertyAnnotations[OneToOne::class];
        $targetEntity       = $oneToOneAnnotation->targetEntity;

        //This is the owing side
        if ($oneToOneAnnotation->inversedBy) {
            $this->assertArrayHasKey(
                key: JoinColumn::class,
                array: $propertyAnnotations,
                message: sprintf("Joincolumn should exists for %s in %s", $propertyName, $entity::class)
            );
            /** @var RepeatableAttributeCollection $repeatableJoinColumnAnnotation */
            $repeatableJoinColumnAnnotation = $propertyAnnotations[JoinColumn::class];

            /** @var JoinColumn $joinColumnAnnotation */
            foreach ($repeatableJoinColumnAnnotation as $joinColumnAnnotation) {
                //The relelationship is not nullable, and we force initialisation
                //We we expect to find an entity
                if (!$joinColumnAnnotation->nullable) {
                    self::assertInstanceOf(
                        expected: $targetEntity,
                        actual: $entity->$getter(),
                        message: sprintf(
                            'Property %s on %s cannot be null',
                            $propertyName,
                            $entity::class,
                        )
                    );
                }

                //The relationship is nullable, so we expect to find null, that we can set null and that we can set null
                if ($joinColumnAnnotation->nullable) {
                    self::assertNull(
                        actual: $entity->$getter(),
                        message: sprintf(
                            'Property %s on %s should be null, %s found',
                            $propertyName,
                            $entity::class,
                            null === $entity->$getter() ? null : $entity->$getter()::class
                        )
                    );
                    $entity->$setter(null);
                    self::assertNull(actual: $entity->$getter());
                }
            }
        }

        //and we should be able to set the targetentity
        $entity->$setter(new $targetEntity());
        self::assertInstanceOf(expected: $targetEntity, actual: $entity->$getter());

        //And we need to do this with an additional propererty
        $targetEntity = new $targetEntity();
        $targetEntity->setId(1);
        $entity->$setter($targetEntity);
        self::assertEquals(expected: 1, actual: $entity->$getter()->getId());
    }

    protected function analyseManyToOneMapping(
        AbstractEntity $entity,
        string $propertyName,
        string $getter,
        string $setter,
        array $propertyAnnotations
    ): void {
        /** @var ManyToOne $manyToOneAnnotation */
        $manyToOneAnnotation = $propertyAnnotations[ManyToOne::class];
        $targetEntity        = $manyToOneAnnotation->targetEntity;

        /** @var RepeatableAttributeCollection $repeatableJoinColumnAnnotation */
        $repeatableJoinColumnAnnotation = $propertyAnnotations[JoinColumn::class];

        /** @var JoinColumn $joinColumnAnnotation */
        foreach ($repeatableJoinColumnAnnotation as $joinColumnAnnotation) {
            //The relelationship is not nullable, and we force initialisation
            //We we expect to find an entity
            if (!$joinColumnAnnotation->nullable) {
                self::assertInstanceOf(
                    expected: $targetEntity,
                    actual: $entity->$getter(),
                    message: sprintf(
                        'Property %s on %s cannot be null',
                        $propertyName,
                        $entity::class,
                    )
                );
            }

            //The relationship is nullable, so we expect to find null, that we can set null and that we can set null
            if ($joinColumnAnnotation->nullable) {
                self::assertNull(
                    actual: $entity->$getter(),
                    message: sprintf(
                        'Property %s on %s should be null, %s found',
                        $propertyName,
                        $entity::class,
                        null === $entity->$getter() ? 'null' : $entity->$getter()::class
                    )
                );
                $entity->$setter(null);
                self::assertNull(actual: $entity->$getter());
            }
        }

        //and we should be able to set the targetentity
        $entity->$setter(new $targetEntity());
        self::assertInstanceOf(expected: $targetEntity, actual: $entity->$getter());

        //And we need to do this with an additional propererty
        $targetEntity = new $targetEntity();
        $targetEntity->setId(1);
        $entity->$setter($targetEntity);
        self::assertEquals(expected: 1, actual: $entity->$getter()->getId());
    }

    protected function analyseManyToManyMapping(
        AbstractEntity $entity,
        string $propertyName,
        string $getter,
        string $setter,
        array $propertyAnnotations
    ): void {
        //We expect an collection from the start
        self::assertInstanceOf(expected: ArrayCollection::class, actual: $entity->$getter());

        //And we need to be able to set a collection
        $collection = new ArrayCollection();
        $entity->$setter($collection);
        $this->assertEquals(expected: $collection, actual: $entity->$getter());
    }
}
