<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Php80\ValueObject\AnnotationToAttribute;
use Rector\Set\ValueObject\LevelSetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // get parameters
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/src'
    ]);
    $parameters->set(Option::AUTO_IMPORT_NAMES, true);

    // Define what rule sets will be applied
    $containerConfigurator->import(LevelSetList::UP_TO_PHP_80);

    $services = $containerConfigurator->services();
    $services->set(AnnotationToAttributeRector::class)
        ->configure(
            [
                new AnnotationToAttribute('ORM\Column'),
                new AnnotationToAttribute('ORM\Id'),
                new AnnotationToAttribute('ORM\Table'),
                new AnnotationToAttribute('ORM\UniqueConstraint'),
                new AnnotationToAttribute('ORM\Entity'),
                new AnnotationToAttribute('ORM\GeneratedValue'),
                new AnnotationToAttribute('ORM\ComposedObject'),
                new AnnotationToAttribute('ORM\OneToMany'),
                new AnnotationToAttribute('ORM\ManyToOne'),
                new AnnotationToAttribute('ORM\OneToOne'),
                new AnnotationToAttribute('ORM\ManyToMany'),
                new AnnotationToAttribute('ORM\JoinColumn'),
                new AnnotationToAttribute('ORM\JoinTable'),
                new AnnotationToAttribute('ORM\OrderBy'),
                new AnnotationToAttribute('Gedmo\Timestampable'),
                new AnnotationToAttribute('Gedmo\Sortable'),
                new AnnotationToAttribute('Gedmo\SortablePosition'),
                new AnnotationToAttribute('Gedmo\Slug'),
                new AnnotationToAttribute('Annotation\Type'),
                new AnnotationToAttribute('Annotation\Hydrator'),
                new AnnotationToAttribute('Annotation\Name'),
                new AnnotationToAttribute('Annotation\Instance'),
                new AnnotationToAttribute('Annotation\Exclude'),
                new AnnotationToAttribute('Annotation\Options'),
                new AnnotationToAttribute('Annotation\Attributes'),
            ]
        );

    // get services (needed for register a single rule)
    // $services = $containerConfigurator->services();

    // register a single rule
    // $services->set(TypedPropertyRector::class);
};
