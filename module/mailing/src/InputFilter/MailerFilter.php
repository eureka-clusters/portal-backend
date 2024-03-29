<?php

declare(strict_types=1);

namespace Mailing\InputFilter;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator\UniqueObject;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\StringLength;
use Mailing\Entity\Mailer;

final class MailerFilter extends InputFilter
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
                            'object_repository' => $entityManager->getRepository(entityName: Mailer::class),
                            'object_manager'    => $entityManager,
                            'use_context'       => true,
                            'fields'            => ['name'],
                        ],
                    ],
                ],
            ]
        );

        $inputFilter->add(
            input: [
                'name'     => 'port',
                'required' => false,
            ]
        );

        $this->add(input: $inputFilter, name: 'mailing_entity_mailer');
    }
}
