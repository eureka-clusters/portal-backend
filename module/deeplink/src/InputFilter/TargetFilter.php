<?php

declare(strict_types=1);

namespace Deeplink\InputFilter;

use Deeplink\Entity\Target;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator\UniqueObject;
use Laminas\InputFilter\InputFilter;
use Laminas\Router\Http\TreeRouteStack;
use Laminas\Validator\Callback;
use RuntimeException;

final class TargetFilter extends InputFilter
{
    public function __construct(EntityManager $entityManager, TreeRouteStack $routeStack)
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(
            [
                'name'       => 'target',
                'required'   => true,
                'validators' => [
                    [
                        'name'    => UniqueObject::class,
                        'options' => [
                            'object_repository' => $entityManager->getRepository(Target::class),
                            'object_manager'    => $entityManager,
                            'use_context'       => true,
                            'fields'            => ['target'],
                        ],
                    ],
                ],
            ]
        );

        $inputFilter->add(
            [
                'name'       => 'route',
                'required'   => true,
                'validators' => [
                    [
                        'name'    => Callback::class,
                        'options' => [
                            'messages' => [
                                Callback::INVALID_VALUE => 'The selected route %value% does not exist',
                            ],
                            'callback' => static function ($value) use ($routeStack) {
                                /*
                                 * Try to assemble a route and if it succeeds return true
                                 */
                                try {
                                    $routeStack->assemble([], ['name' => $value]);

                                    return true;
                                } catch (RuntimeException) {
                                    return false;
                                }
                            },
                        ],
                    ],
                ],
            ]
        );

        $this->add($inputFilter, 'deeplink_entity_target');
    }
}
