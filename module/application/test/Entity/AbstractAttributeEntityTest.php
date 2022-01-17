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
use function get_class;
use function implode;
use function is_array;
use function sprintf;
use function str_replace;
use function ucfirst;

abstract class AbstractAttributeEntityTest extends TestCase
{
    public function canCreateEntitiesAndSaveTxtFields(string $namespace, string $baseFolder): void
    {
        $entities = $this->getEntities($namespace, $baseFolder);

        $labels = [];
        foreach ($entities as $className => $reflectionClass) {
            $builder      = new AttributeBuilder();
            $dataFieldset = $builder->createForm(new $className());

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

                $this->assertIsArray($element->getAttributes());
                $this->assertIsArray($element->getOptions());
            }

            foreach ($reflectionClass->getStaticProperties() as $constant) {
                if (is_array($constant)) {
                    foreach ($constant as $constantValue) {
                        $labels[] = $constantValue;
                    }
                }
            }
        }

        file_put_contents(
            $baseFolder . '/../../config/language.php',
            "<?php\n\ndeclare(strict_types=1);\n\n_('" . implode("');\n_('", array_unique($labels)) . "');\n"
        );
    }

    /**
     * @return array
     */
    protected function getEntities(string $namespace, string $baseFolder): array
    {
        $scanFolder = $baseFolder . '/../../src/Entity';

        $finder = new Finder();
        $finder->files()->name('*.php')->in($scanFolder);

        $entities = [];

        foreach ($finder as $fileInfo) {
            $reflectionClass = $this->getReflectionClassFromFileInfo($namespace, $fileInfo);

            if ($reflectionClass->isInstantiable()) {
                $className            = $this->getClassNameFromFileInfo($namespace, $fileInfo);
                $entities[$className] = $reflectionClass;
            }
        }

        return $entities;
    }

    protected function getReflectionClassFromFileInfo(string $namespace, SplFileInfo $fileInfo): ReflectionClass
    {
        return new ReflectionClass($this->getClassNameFromFileInfo($namespace, $fileInfo));
    }

    protected function getClassNameFromFileInfo(string $namespace, SplFileInfo $fileInfo): string
    {
        return ucfirst($namespace) . '\Entity\\' . str_replace(
                ['/', '.php'],
                ['\\', ''],
                $fileInfo->getRelativePathname()
            );
    }

    protected function analyseClass(ReflectionClass $class): void
    {
        $builder          = new AttributeReader();
        $classAnnotations = $builder->getClassAnnotations($class);

        self::assertArrayHasKey(Table::class, $classAnnotations, sprintf('%s should have a table', $class->getName()));
        self::assertArrayHasKey(
            Entity::class,
            $classAnnotations,
            sprintf('%s should have an entity', $class->getName())
        );

        //Get the entity annotation
        /** @var Entity $entityAnnotation */
        $entityAnnotation = $classAnnotations[Entity::class];
        if (null !== $entityAnnotation->repositoryClass) {
            self::assertTrue(
                class_exists($entityAnnotation->repositoryClass),
                sprintf(
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
        $propertyAnnotations = $builder->getPropertyAnnotations($property);

        $propertyName = $property->getName();

        //produce the getters and setters
        $setter = 'set' . ucfirst($propertyName);
        $getter = 'get' . ucfirst($propertyName);

        switch (true) {
            case array_key_exists(key: Column::class, array: $propertyAnnotations):
                $this->analyseColumnMapping($entity, $propertyName, $getter, $setter, $propertyAnnotations);
                break;
            case array_key_exists(key: OneToMany::class, array: $propertyAnnotations):
                $this->analyseOneToManyMapping($entity, $propertyName, $getter, $setter, $propertyAnnotations);
                break;
            case array_key_exists(key: OneToOne::class, array: $propertyAnnotations):
                $this->analyseOneToOneMapping($entity, $propertyName, $getter, $setter, $propertyAnnotations);
                break;
            case array_key_exists(key: ManyToOne::class, array: $propertyAnnotations):
                $this->analyseManyToOneMapping($entity, $propertyName, $getter, $setter, $propertyAnnotations);
                break;
            case array_key_exists(key: ManyToMany::class, array: $propertyAnnotations):
                $this->analyseManyToManyMapping($entity, $propertyName, $getter, $setter, $propertyAnnotations);
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
            self::assertNull($entity->$getter());
        }

        if (!$columnAnnotation->nullable) {
            switch ($columnAnnotation->type) {
                case 'integer':
                case 'smallint':
                    if ($getter !== 'getId') {
                        self::assertNotNull(
                            $entity->$getter(),
                            sprintf('%s on %s should not be null', $getter, $entity::class)
                        );
                    }
                    $entity->$setter(1);
                    self::assertEquals(1, $entity->$getter());
                    break;
                case 'datetime':
                    $new = new DateTime();
                    self::assertNotNull(
                        $entity->$getter(),
                        sprintf('%s on %s should not be null', $getter, $entity::class)
                    );
                    $entity->$setter($new);
                    self::assertEquals($new, $entity->$getter());
                    break;
                case 'string':
                case 'text':
                    self::assertNotNull(
                        $entity->$getter(),
                        sprintf('%s on %s should not be null', $getter, $entity::class)
                    );
                    $entity->$setter('this is a string');
                    self::assertEquals('this is a string', $entity->$getter());
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
        self::assertInstanceOf(ArrayCollection::class, $entity->$getter());

        //And we need to be able to set a collection
        $collection = new ArrayCollection();
        $entity->$setter($collection);
        $this->assertEquals($collection, $entity->$getter());
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
                JoinColumn::class,
                $propertyAnnotations,
                sprintf("Joincolumn should exists for %s in %s", $propertyName, $entity::class)
            );
            /** @var RepeatableAttributeCollection $repeatableJoinColumnAnnotation */
            $repeatableJoinColumnAnnotation = $propertyAnnotations[JoinColumn::class];

            foreach ($repeatableJoinColumnAnnotation as $joinColumnAnnotation) {
                //The relelationship is not nullable, and we force initialisation
                //We we expect to find an entity
                if (!$joinColumnAnnotation->nullable) {
                    self::assertInstanceOf(
                        $targetEntity,
                        $entity->$getter(),
                        sprintf(
                            'Property %s on %s cannot be null',
                            $propertyName,
                            $entity::class,
                        )
                    );
                }

                //The relationship is nullable, so we expect to find null, that we can set null and that we can set null
                if ($joinColumnAnnotation->nullable) {
                    self::assertNull(
                        $entity->$getter(),
                        sprintf(
                            'Property %s on %s should be null, %s found',
                            $propertyName,
                            $entity::class,
                            null === $entity->$getter() ? null : get_class($entity->$getter())
                        )
                    );
                    $entity->$setter(null);
                    self::assertNull($entity->$getter());
                }
            }
        }

        //and we should be able to set the targetentity
        $entity->$setter(new $targetEntity());
        self::assertInstanceOf($targetEntity, $entity->$getter());

        //And we need to do this with an additional propererty
        $targetEntity = new $targetEntity();
        $targetEntity->setId(1);
        $entity->$setter($targetEntity);
        self::assertEquals(1, $entity->$getter()->getId());
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

        foreach ($repeatableJoinColumnAnnotation as $joinColumnAnnotation) {
            //The relelationship is not nullable, and we force initialisation
            //We we expect to find an entity
            if (!$joinColumnAnnotation->nullable) {
                self::assertInstanceOf(
                    $targetEntity,
                    $entity->$getter(),
                    sprintf(
                        'Property %s on %s cannot be null',
                        $propertyName,
                        $entity::class,
                    )
                );
            }

            //The relationship is nullable, so we expect to find null, that we can set null and that we can set null
            if ($joinColumnAnnotation->nullable) {
                self::assertNull(
                    $entity->$getter(),
                    sprintf(
                        'Property %s on %s should be null, %s found',
                        $propertyName,
                        $entity::class,
                        null === $entity->$getter() ? 'null' : get_class($entity->$getter())
                    )
                );
                $entity->$setter(null);
                self::assertNull($entity->$getter());
            }
        }

        //and we should be able to set the targetentity
        $entity->$setter(new $targetEntity());
        self::assertInstanceOf($targetEntity, $entity->$getter());

        //And we need to do this with an additional propererty
        $targetEntity = new $targetEntity();
        $targetEntity->setId(1);
        $entity->$setter($targetEntity);
        self::assertEquals(1, $entity->$getter()->getId());
    }

    protected function analyseManyToManyMapping(
        AbstractEntity $entity,
        string $propertyName,
        string $getter,
        string $setter,
        array $propertyAnnotations
    ): void {
        //We expect an collection from the start
        self::assertInstanceOf(ArrayCollection::class, $entity->$getter());

        //And we need to be able to set a collection
        $collection = new ArrayCollection();
        $entity->$setter($collection);
        $this->assertEquals($collection, $entity->$getter());
    }
}
