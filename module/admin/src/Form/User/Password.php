<?php

declare(strict_types=1);

namespace Admin\Form\User;

use JetBrains\PhpStorm\ArrayShape;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\Callback;
use Laminas\Validator\StringLength;

use function _;
use function preg_match;

final class Password extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct();
        $this->setAttribute(key: 'action', value: '');
        $this->setAttribute(key: 'class', value: 'form-horizontal');

        $this->add(
            elementOrFieldset: [
                'type'       => \Laminas\Form\Element\Password::class,
                'name'       => 'password',
                'options'    => [
                    'help-block' => _("txt-new-password-form-help"),
                ],
                'attributes' => [
                    'label' => _("txt-new-password"),
                ],
            ]
        );
        $this->add(
            elementOrFieldset: [
                'type'       => \Laminas\Form\Element\Password::class,
                'name'       => 'passwordVerify',
                'options'    => [
                    'help-block' => _("txt-new-password-verify-form-help"),
                ],
                'attributes' => [
                    'label' => _("txt-new-password-verify"),
                ],
            ]
        );
        $this->add(
            elementOrFieldset: [
                'type'    => Csrf::class,
                'name'    => 'csrf',
                'options' => [
                    'csrf_options' => [
                        'timeout' => 1200,
                    ],
                ],
            ]
        );
        $this->add(
            elementOrFieldset: [
                'type'       => Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'class' => "btn btn-primary",
                    'value' => _("txt-submit"),
                ],
            ]
        );
        $this->add(
            elementOrFieldset: [
                'type'       => Submit::class,
                'name'       => 'cancel',
                'attributes' => [
                    'class' => "btn btn-warning",
                    'value' => _("txt-cancel"),
                ],
            ]
        );
    }

    #[ArrayShape(shape: ['password' => "array", 'passwordVerify' => "array"])]
    public function getInputFilterSpecification(): array
    {
        return [
            'password'       => [
                'required'   => true,
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'min' => 12,
                        ],
                    ],
                    [
                        'name'    => Callback::class,
                        'options' => [
                            'messages' => [
                                Callback::INVALID_VALUE
                                => 'The password requires at least 1 UPPERCASE character, none found',
                            ],
                            'callback' => static fn ($value) => preg_match(pattern: '@[A-Z]@', subject: (string) $value),
                        ],
                    ],
                    [
                        'name'    => Callback::class,
                        'options' => [
                            'messages' => [
                                Callback::INVALID_VALUE
                                => 'The password requires at least 1 lowercase character, none found',
                            ],
                            'callback' => static fn ($value) => preg_match(pattern: '@[a-z]@', subject: (string) $value),
                        ],
                    ],
                    [
                        'name'    => Callback::class,
                        'options' => [
                            'messages' => [
                                Callback::INVALID_VALUE => 'The password requires at least 1 number, none found',
                            ],
                            'callback' => static fn ($value) => preg_match(pattern: '@[\d]@', subject: (string) $value),
                        ],
                    ],
                ],
            ],
            'passwordVerify' => [
                'required'   => true,
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'min' => 12,
                        ],
                    ],
                    [
                        'name'    => 'Identical',
                        'options' => [
                            'token' => 'password',
                        ],
                    ],
                ],
            ],
        ];
    }
}
