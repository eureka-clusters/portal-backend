<?php

declare(strict_types=1);

namespace Admin\Form\User;

use Api\Entity\OAuth\Client;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntitySelect;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;

use function _;

final class GenerateTokenForm extends Form
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();

        $this->add(
            elementOrFieldset: [
                'type'       => EntitySelect::class,
                'name'       => 'client',
                'attributes' => [
                    'label' => _("txt-generate-token-client-label"),
                ],
                'options'    => [
                    'help-block'     => _("txt-generate-token-client-help-block"),
                    'empty_option'   => _("txt-generate-token-client-empty-option"),
                    'object_manager' => $entityManager,
                    'target_class'   => Client::class,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [],
                            'orderBy'  => [
                                'description' => Criteria::ASC,
                            ],
                        ],
                    ],
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
}
