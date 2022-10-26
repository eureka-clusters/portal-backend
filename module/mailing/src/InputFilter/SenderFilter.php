<?php

declare(strict_types=1);

namespace Mailing\InputFilter;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator\UniqueObject;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\StringLength;
use Mailing\Entity\Sender;

final class SenderFilter extends InputFilter
{
    public function __construct(EntityManager $entityManager)
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(
            input: [
                'name'       => 'sender',
                'required'   => true,
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 3,
                            'max'      => 150,
                        ],
                    ],
                    [
                        'name'    => UniqueObject::class,
                        'options' => [
                            'object_repository' => $entityManager->getRepository(entityName: Sender::class),
                            'object_manager'    => $entityManager,
                            'use_context'       => true,
                            'fields'            => ['sender'],
                        ],
                    ],
                ],
            ]
        );

        $inputFilter->add(
            input: [
                'name'       => 'email',
                'required'   => true,
                'validators' => [
                    [
                        'name' => 'EmailAddress',
                    ],
                ],
            ]
        );

        $this->add(input: $inputFilter, name: 'mailing_entity_sender');
    }
}
