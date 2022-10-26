<?php

declare(strict_types=1);

namespace Mailing\InputFilter;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator\UniqueObject;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\InputFilter\InputFilter;
use Mailing\Entity\Template;

final class TemplateFilter extends InputFilter
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
                            'max'      => 100,
                        ],
                    ],
                    [
                        'name'    => UniqueObject::class,
                        'options' => [
                            'object_repository' => $entityManager->getRepository(Template::class),
                            'object_manager'    => $entityManager,
                            'use_context'       => true,
                            'fields'            => ['name'],
                        ],
                    ],
                ],
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'subject',
                'required' => true,
                'filters'  => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
            ]
        );

        $this->add($inputFilter, 'mailing_entity_template');
    }
}
