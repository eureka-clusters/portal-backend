<?php

declare(strict_types=1);

namespace Api\InputFilter\OAuth;

use Api\Entity\OAuth\Service;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator\UniqueObject;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\StringLength;

class ServiceFilter extends InputFilter
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
                            'min'      => 2,
                            'max'      => 255,
                        ],
                    ],
                    [
                        'name'    => UniqueObject::class,
                        'options' => [
                            'object_repository' => $entityManager->getRepository(entityName: Service::class),
                            'object_manager'    => $entityManager,
                            'use_context'       => true,
                            'fields'            => 'name',
                        ],
                    ],
                ],
            ]
        );


        $inputFilter->add(
            input: [
                'name'     => 'description',
                'required' => false,
                'filters'  => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
            ]
        );

        $inputFilter->add(
            input: [
                'name'     => 'clientId',
                'required' => true,
                'filters'  => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
            ]
        );

        $inputFilter->add(
            input: [
                'name'     => 'clientSecret',
                'required' => false,
            ]
        );

        $inputFilter->add(
            input: [
                'name'     => 'redirectUrl',
                'required' => false,
                'filters'  => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
            ]
        );

        $inputFilter->add(
            input: [
                'name'     => 'authorizationUrl',
                'required' => false,
                'filters'  => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
            ]
        );

        $inputFilter->add(
            input: [
                'name'     => 'accessTokenUrl',
                'required' => false,
                'filters'  => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
            ]
        );
        $inputFilter->add(
            input: [
                'name'     => 'profileUrl',
                'required' => false,
                'filters'  => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
            ]
        );

        $inputFilter->add(
            input: [
                'name'     => 'allowedClusters',
                'required' => false,
            ]
        );

        $inputFilter->add(
            input: [
                'name'     => 'client',
                'required' => false,
            ]
        );

        $inputFilter->add(
            input: [
                'name'     => 'scope',
                'required' => true,
            ]
        );

        $this->add(input: $inputFilter, name: 'api_entity_oauth_service');
    }
}
