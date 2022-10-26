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
            [
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
            [
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
                            'object_repository' => $entityManager->getRepository(Transactional::class),
                            'object_manager'    => $entityManager,
                            'use_context'       => true,
                            'fields'            => ['key'],
                        ],
                    ],
                ],
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'mailSubject',
                'required' => true,
                'filters'  => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'mailHtml',
                'required' => true,
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'template',
                'required' => true,
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'sender',
                'required' => true,
            ]
        );

        $this->add($inputFilter, 'mailing_entity_transactional');
    }
}
