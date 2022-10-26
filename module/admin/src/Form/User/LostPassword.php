<?php

declare(strict_types=1);

namespace Admin\Form\User;

use Laminas\Filter\StringTrim;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;

use function _;

final class LostPassword extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct();
        $this->setAttribute(key: 'action', value: '');
        $this->setAttribute(key: 'class', value: 'form-horizontal');

        $this->add(
            elementOrFieldset: [
                'name' => 'email',
                'type' => Email::class,
                'options' => [
                    'label' => _('txt-company-email-address'),
                    'help-block' => _('txt-lost-password-help-block'),
                ],
                'attributes' => [
                    'placeholder' => _('txt-company-email-address'),
                    'class' => 'form-control',
                ],
            ]
        );
        $this->add(
            elementOrFieldset: [
                'type' => Csrf::class,
                'name' => 'csrf',
                'options' => [
                    'csrf_options' => [
                        'timeout' => 1200,
                    ],
                ],
            ]
        );
        $this->add(
            elementOrFieldset: [
                'type' => Submit::class,
                'name' => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-reset-password'),
                ],
            ]
        );
        $this->add(
            elementOrFieldset: [
                'type' => Submit::class,
                'name' => 'cancel',
                'attributes' => [
                    'class' => 'btn btn-warning',
                    'value' => _('txt-cancel'),
                ],
            ]
        );
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'email' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => StringTrim::class,
                    ],
                ],
                'validators' => [
                    [
                        'name' => 'EmailAddress',
                    ],
                ],
            ],
        ];
    }
}
