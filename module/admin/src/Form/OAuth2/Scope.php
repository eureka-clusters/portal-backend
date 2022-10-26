<?php

declare(strict_types=1);

namespace Admin\Form\OAuth2;

use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;

use function _;

final class Scope extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');
        $this->setAttribute('class', 'form-horizontal');

        $this->add(
            [
                'type'       => Text::class,
                'name'       => 'type',
                'options'    => [
                    'help-block' => _("txt-oauth2-scope-select-type-help-block"),
                ],
                'attributes' => [
                    'label'       => _("txt-oauth2-scope-type-label"),
                    'placeholder' => _("txt-oauth2-scope-type-placeholder"),
                ],
            ]
        );

        $this->add(
            [
                'type'       => Text::class,
                'name'       => 'scope',
                'options'    => [
                    'help-block' => _("txt-oauth2-scope-select-scope-help-block"),
                ],
                'attributes' => [
                    'label'       => _("txt-oauth2-scope-scope-label"),
                    'placeholder' => _("txt-oauth2-scope-scope-placeholder"),
                ],
            ]
        );

        $this->add(
            [
                'type'       => Checkbox::class,
                'name'       => 'is_default',
                'options'    => [
                    'help-block'    => _("txt-oauth2-scope-is-default-help-block"),
                    'checked_value' => '1',
                ],
                'attributes' => [
                    'label' => _("txt-oauth2-scope-is-default-label"),
                ],
            ]
        );

        $this->add(
            [
                'type' => Csrf::class,
                'name' => 'csrf',
            ]
        );
        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'cancel',
                'attributes' => [
                    'class' => "btn btn-warning",
                    'value' => _("txt-cancel"),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'delete',
                'attributes' => [
                    'class' => "btn btn-danger",
                    'value' => _("txt-delete"),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'class' => "btn btn-primary",
                    'value' => _("txt-submit"),
                ],
            ]
        );
    }

    public function getInputFilterSpecification(): array
    {
        return [
            [
                'name'     => 'type',
                'required' => true,
            ],
            [
                'name'     => 'scope',
                'required' => true,
            ],
            [
                'name'     => 'is_default',
                'required' => false,
            ],
        ];
    }
}
