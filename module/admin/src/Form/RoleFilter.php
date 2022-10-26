<?php

declare(strict_types=1);

namespace Admin\Form;

use Laminas\Form\Element\MultiCheckbox;
use Laminas\Form\Fieldset;
use Search\Form\SearchFilter;

use function _;

final class RoleFilter extends SearchFilter
{
    public function __construct()
    {
        parent::__construct();

        $filterFieldset = new Fieldset(name: 'filter');

        $filterFieldset->add(
            elementOrFieldset: [
                'type'       => MultiCheckbox::class,
                'name'       => 'locked',
                'options'    => [
                    'value_options' => [1 => 'locked'],
                ],
                'attributes' => [
                    'label' => _("txt-filter-on-locked"),
                ],
            ]
        );

        $this->add($filterFieldset);
    }
}
