<?php

declare(strict_types=1);

namespace Admin\Form\User;

use Laminas\Filter\StringTrim;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;

use function _;

final class Login extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct();

        $this->setAttribute('action', '');
        $this->setAttribute('class', 'form-horizontal');

        $this->add(
            [
                'name' => 'username',
                'type' => Email::class,
                'options' => [
                    'label' => _('txt-identity'),
                ],
                'attributes' => [
                    'placeholder' => _('txt-company-email-address'),
                    'class' => 'form-control',
                ],
            ]
        );
        $this->add(
            [
                'name' => 'password',
                'type' => Password::class,
                'options' => [
                    'label' => _('txt-password'),
                ],
                'attributes' => [
                    'placeholder' => _('txt-password'),
                    'class' => 'form-control',
                ],
            ]
        );
        $this->add(
            [
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
            [
                'name' => 'submit',
                'type' => Submit::class,
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-login'),
                ],
            ]
        );
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'username' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => StringTrim::class,
                    ],
                ],
            ],
            'password' => [
                'required' => true,
            ],
        ];
    }
}
