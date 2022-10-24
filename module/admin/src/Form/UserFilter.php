<?php

declare(strict_types=1);

namespace Admin\Form;

use Admin\Entity\Role;
use Admin\Entity\User;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;
use Laminas\Form\Element\MultiCheckbox;
use Laminas\Form\Fieldset;
use Search\Form\SearchFilter;

use function _;

final class UserFilter extends SearchFilter
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();

        $filterFieldset = new Fieldset('filter');

        $filterFieldset->add(
            [
                'type'       => EntityMultiCheckbox::class,
                'name'       => 'roles',
                'attributes' => [
                    'label' => _("txt-filter-on-role"),
                ],
                'options'    => [
                    'object_manager' => $entityManager,
                    'target_class'   => Role::class,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [],
                            'orderBy'  => [
                                'name' => Criteria::ASC,
                            ],
                        ],
                    ],
                ],
            ]
        );

        $filterFieldset->add(
            [
                'type'       => MultiCheckbox::class,
                'name'       => 'status',
                'options'    => [
                    'value_options' => User::getStatusTemplates(),
                ],
                'attributes' => [
                    'label' => _("txt-filter-on-status"),
                ],
            ]
        );

        $this->add($filterFieldset);
    }
}
