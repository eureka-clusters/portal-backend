<?php

declare(strict_types=1);

namespace Admin\Form\OAuth2;

use Laminas\Form\Element\Url;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Api\Entity\OAuth\Scope;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Submit;
use Api\Entity;
use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntitySelect;
use Laminas\Form\Element;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;

use function _;

final class Client extends Form implements InputFilterProviderInterface
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');
        $this->setAttribute('class', 'form-horizontal');

        $this->add(
            [
                'type'       => Url::class,
                'name'       => 'redirectUri',
                'options'    => [
                    'help-block' => _("txt-oauth2-client-select-a-valid-redirect-uri-help-block"),
                ],
                'attributes' => [
                    'label'       => _("txt-redirect-uri"),
                    'placeholder' => _("txt-redirect-uri"),
                ],
            ]
        );

        $this->add(
            [
                'type'       => Text::class,
                'name'       => 'name',
                'options'    => [
                    'help-block' => _("txt-oauth2-client-name-help-block"),
                ],
                'attributes' => [
                    'label'       => _("txt-oauth2-client-name-label"),
                    'placeholder' => _("txt-oauth2-client-name-placeholder"),
                ],
            ]
        );

        $this->add(
            [
                'type'       => Textarea::class,
                'name'       => 'description',
                'options'    => [
                    'help-block' => _("txt-oauth2-client-description-help-block"),
                ],
                'attributes' => [
                    'label'       => _("txt-oauth2-client-description-label"),
                    'placeholder' => _("txt-oauth2-client-description-placeholder"),
                ],
            ]
        );

        $this->add(
            [
                'type'       => Text::class,
                'name'       => 'grantTypes',
                'options'    => [
                    'help-block' => _("txt-oauth2-client-grant-types-help-block"),
                ],
                'attributes' => [
                    'label'       => _("txt-oauth2-client-grant-types-label"),
                    'placeholder' => _("txt-oauth2-client-grant-types-placeholder"),
                ],
            ]
        );

        $this->add(
            [
                'type'       => EntitySelect::class,
                'name'       => 'scope',
                'options'    => [
                    'target_class'   => Scope::class,
                    'object_manager' => $entityManager,
                    'empty_option'   => '— Select a scope',
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [],
                            'orderBy'  => [],
                        ],
                    ],
                    'help-block'     => _("txt-oauth2-client-select-a-valid-scope-help-block"),
                ],
                'attributes' => [
                    'label' => _("txt-oauth2-client-select-a-valid-scope-label"),
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
                'name'     => 'redirectUri',
                'required' => true,
            ],
            [
                'name'     => 'scope',
                'required' => true,
            ],
            [
                'name'     => 'name',
                'required' => true,
            ],
            [
                'name'     => 'description',
                'required' => true,
            ],
        ];
    }
}
