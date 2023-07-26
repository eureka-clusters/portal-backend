<?php

declare(strict_types=1);

namespace Admin\InputFilter;

use Admin\Entity\User;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator\UniqueObject;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\EmailAddress;

final class UserFilter extends InputFilter
{
    public function __construct(EntityManager $entityManager)
    {
        $inputFilter = new InputFilter();

        $inputFilter->add(
            input: [
                'name'     => 'firstName',
                'required' => true,
            ]
        );

        $inputFilter->add(
            input: [
                'name'     => 'lastName',
                'required' => true,
            ]
        );

        $inputFilter->add(
            input: [
                'name'       => 'email',
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => EmailAddress::class,
                    ],
                    [
                        'name'    => UniqueObject::class,
                        'options' => [
                            'object_repository' => $entityManager->getRepository(entityName: User::class),
                            'object_manager'    => $entityManager,
                            'use_context'       => true,
                            'fields'            => 'email',
                        ],
                    ],
                ],
            ]
        );

        $inputFilter->add(
            input: [
                'name'     => 'roles',
                'required' => false,
            ]
        );

        $this->add(input: $inputFilter, name: 'admin_entity_user');
    }
}
