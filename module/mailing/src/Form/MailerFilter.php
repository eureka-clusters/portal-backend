<?php

declare(strict_types=1);

namespace Mailing\Form;

use Application\Form\SearchFilter;
use Laminas\Form\Element\MultiCheckbox;
use Laminas\Form\Fieldset;
use Mailing\Entity\Mailer;

use function _;

final class MailerFilter extends SearchFilter
{
    public function __construct()
    {
        parent::__construct();
        $this->setAttribute(key: 'method', value: 'get');
        $this->setAttribute(key: 'action', value: '');

        $filterFieldset = new Fieldset(name: 'filter');

        $filterFieldset->add(
            elementOrFieldset: [
                'type'       => MultiCheckbox::class,
                'name'       => 'isActive',
                'options'    => [
                    'value_options' => [1 => _('txt-only-inactive')],
                    'inline'        => true,
                ],
                'attributes' => [
                    'label' => _('txt-include-deleted'),
                ],
            ]
        );

        $filterFieldset->add(
            elementOrFieldset: [
                'type'       => MultiCheckbox::class,
                'name'       => 'tags',
                'options'    => [
                    'value_options' => Mailer::getServicesArray(),
                    'inline'        => true,
                ],
                'attributes' => [
                    'label' => _('txt-filter-on-tags'),
                ],
            ]
        );

        $this->add(elementOrFieldset: $filterFieldset);
    }
}
