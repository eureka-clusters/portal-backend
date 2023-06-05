<?php

declare(strict_types=1);

namespace Cluster\InputFilter\Cluster;

use Cluster\Entity\Cluster\Group;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator\UniqueObject;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\StringLength;

final class GroupFilter extends InputFilter
{
    public function __construct(EntityManager $entityManager)
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(
            input: [
                'name'       => 'name',
                'required'   => true,
                'filters'    => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 255,
                        ],
                    ],
                    [
                        'name'    => UniqueObject::class,
                        'options' => [
                            'object_repository' => $entityManager->getRepository(entityName: Group::class),
                            'object_manager'    => $entityManager,
                            'use_context'       => true,
                            'fields'            => 'name',
                        ],
                    ],
                ],
            ]
        );

        $this->add(input: $inputFilter, name: 'cluster_entity_cluster_group');
    }
}
