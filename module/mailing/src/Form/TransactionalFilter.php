<?php

declare(strict_types=1);

namespace Mailing\Form;

use Application\Form\SearchFilter;
use Laminas\Form\Element\MultiCheckbox;
use Laminas\Form\Fieldset;

use function _;

final class TransactionalFilter extends SearchFilter
{
    public function __construct()
    {
        parent::__construct();

        $filterFieldset = new Fieldset('filter');

        $filterFieldset->add(
            [
                'type' => MultiCheckbox::class,
                'name' => 'locked',
                'options' => [
                    'value_options' => [1 => 'locked'],
                ],
                'attributes' => [
                    'label' => _('txt-filter-on-locked'),
                ],
            ]
        );

        $this->add($filterFieldset);
    }
}
