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
            [
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
                            'object_repository' => $entityManager->getRepository(Sender::class),
                            'object_manager'    => $entityManager,
                            'use_context'       => true,
                            'fields'            => ['sender'],
                        ],
                    ],
                ],
            ]
        );

        $inputFilter->add(
            [
                'name'       => 'email',
                'required'   => true,
                'validators' => [
                    [
                        'name' => 'EmailAddress',
                    ],
                ],
            ]
        );

        $this->add($inputFilter, 'mailing_entity_sender');
    }
}
