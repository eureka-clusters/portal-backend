<?php

declare(strict_types=1);

namespace Api\InputFilter\OAuth;

use Api\Entity\OAuth\Service;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator\UniqueObject;
use Laminas\InputFilter\InputFilter;

final class
ServiceFilter extends InputFilter
{
    public function __construct(EntityManager $entityManager)
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(
            input: [
                'name' => 'name',
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 3,
                            'max' => 255,
                        ],
                    ],
                    [
                        'name' => UniqueObject::class,
                        'options' => [
                            'object_repository' => $entityManager->getRepository(entityName: Service::class),
                            'object_manager' => $entityManager,
                            'use_context' => true,
                            'fields' => 'name',
                        ],
                    ],
                ],
            ]
        );

        $this->add(input: $inputFilter, name: 'api_entity_oauth_service');
    }
}
