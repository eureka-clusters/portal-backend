<?php

declare(strict_types=1);

namespace Admin\Form\OAuth2;

use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\Identical;
use Laminas\Validator\StringLength;
use Override;
use function _;

final class UpdateServiceClientSecretForm extends Form implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct();
        $this->setAttribute(key: 'method', value: 'post');
        $this->setAttribute(key: 'action', value: '');
        $this->setAttribute(key: 'class', value: 'form-horizontal');

        $this->add(
            elementOrFieldset: [
                'type'       => Password::class,
                'name'       => 'clientSecret',
                'options'    => [
                    'help-block' => _("txt-oauth2-service-client-secret-help-block"),
                ],
                'attributes' => [
                    'label' => _("txt-oauth2-service-client-secret-label"),
                ],
            ]
        );

        $this->add(
            elementOrFieldset: [
                'type'       => Password::class,
                'name'       => 'clientSecretVerify',
                'options'    => [
                    'help-block' => _("txt-oauth2-service-client-secret-verify-help-block"),
                ],
                'attributes' => [
                    'label' => _("txt-oauth2-service-client-secret-verify-label"),
                ],
            ]
        );


        $this->add(
            elementOrFieldset: [
                'type' => Csrf::class,
                'name' => 'csrf',
            ]
        );

        $this->add(
            elementOrFieldset: [
                'type'       => Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'id'    => 'submit',
                    'class' => 'btn btn-primary',
                    'value' => _('txt-submit'),
                ],
            ]
        );
        $this->add(
            elementOrFieldset: [
                'type'       => Submit::class,
                'name'       => 'cancel',
                'attributes' => [
                    'id'    => 'cancel',
                    'class' => 'btn btn-warning',
                    'value' => _('txt-cancel'),
                ],
            ]
        );

    }

    #[Override]
    public function getInputFilterSpecification(): array
    {
        return [
            'clientSecret'       => [
                'required'   => true,
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'min' => 6,
                        ],
                    ],
                ],
            ],
            'clientSecretVerify' => [
                'required'   => true,
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'min' => 6,
                        ],
                    ],
                    [
                        'name'    => Identical::class,
                        'options' => [
                            'token' => 'clientSecret',
                        ],
                    ],
                ],
            ],
        ];
    }
}
