<?php

declare(strict_types=1);

namespace Mailing\Form;

use Laminas\Form\Element\MultiCheckbox;
use Laminas\Form\Fieldset;
use Mailing\Entity\Mailer;
use Application\Form\SearchFilter;

use function _;

final class MailerFilter extends SearchFilter
{
    public function __construct()
    {
        parent::__construct();
        $this->setAttribute('method', 'get');
        $this->setAttribute('action', '');

        $filterFieldset = new Fieldset('filter');

        $filterFieldset->add(
            [
                'type' => MultiCheckbox::class,
                'name' => 'isActive',
                'options' => [
                    'value_options' => [1 => _('txt-only-inactive')],
                    'inline' => true,
                ],
                'attributes' => [
                    'label' => _('txt-include-deleted'),
                ],
            ]
        );

        $filterFieldset->add(
            [
                'type' => MultiCheckbox::class,
                'name' => 'tags',
                'options' => [
                    'value_options' => Mailer::getServicesArray(),
                    'inline' => true,
                ],
                'attributes' => [
                    'label' => _('txt-filter-on-tags'),
                ],
            ]
        );


        $this->add($filterFieldset);
    }
}
