<?php

declare(strict_types=1);

namespace Mailing\InputFilter;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator\UniqueObject;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\InputFilter\InputFilter;
use Mailing\Entity\Transactional;

final class TransactionalFilter extends InputFilter
{
    public function __construct(EntityManager $entityManager)
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(
            input: [
                'name'       => 'name',
                'required'   => true,
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 3,
                            'max'      => 255,
                        ],
                    ],
                ],
            ]
        );
        $inputFilter->add(
            input: [
                'name'       => 'key',
                'required'   => false,
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 3,
                            'max'      => 255,
                        ],
                    ],
                    [
                        'name'    => UniqueObject::class,
                        'options' => [
                            'object_repository' => $entityManager->getRepository(entityName: Transactional::class),
                            'object_manager'    => $entityManager,
                            'use_context'       => true,
                            'fields'            => ['key'],
                        ],
                    ],
                ],
            ]
        );
        $inputFilter->add(
            input: [
                'name'     => 'mailSubject',
                'required' => true,
                'filters'  => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
            ]
        );
        $inputFilter->add(
            input: [
                'name'     => 'mailHtml',
                'required' => true,
            ]
        );
        $inputFilter->add(
            input: [
                'name'     => 'template',
                'required' => true,
            ]
        );
        $inputFilter->add(
            input: [
                'name'     => 'sender',
                'required' => true,
            ]
        );

        $this->add(input: $inputFilter, name: 'mailing_entity_transactional');
    }
}
