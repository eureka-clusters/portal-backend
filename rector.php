<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Php80\Rector\Class_\DoctrineAnnotationClassToAttributeRector;
use Rector\Php80\ValueObject\AnnotationToAttribute;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths(
        [
            __DIR__ . '/module'
        ]
    );
    $rectorConfig->importNames();
    $rectorConfig->phpVersion(PhpVersion::PHP_81);
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_81
    ]);

    $rectorConfig->rule(AnnotationToAttributeRector::class);
    $rectorConfig->ruleWithConfiguration(TypedPropertyRector::class, [
        TypedPropertyRector::INLINE_PUBLIC => false
    ]);
    $rectorConfig->ruleWithConfiguration(DoctrineAnnotationClassToAttributeRector::class, [
        DoctrineAnnotationClassToAttributeRector::REMOVE_ANNOTATIONS => true,
    ]);

    $rectorConfig->ruleWithConfiguration(
        AnnotationToAttributeRector::class,
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
};
