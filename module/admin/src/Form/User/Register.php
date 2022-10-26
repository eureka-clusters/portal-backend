<?php

declare(strict_types=1);

namespace Admin\Form\User;

use CirclicalRecaptcha\Form\Element\Recaptcha;
use Contact\Entity\OptIn;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;

use function _;
use function sprintf;

final class Register extends Form
{
    public function __construct(private readonly EntityManager $entityManager)
    {
        parent::__construct();
    }

    public function init(): void
    {
        $this->setAttribute('id', 'recaptcha-form');
        $this->setAttribute('action', '');

        $this->add(
            [
                'name' => 'firstName',
                'type' => Text::class,
                'options' => [
                    'label' => _('txt-first-name'),
                ],
                'attributes' => [
                    'placeholder' => _('txt-give-your-first-name'),
                ],
            ]
        );
        $this->add(
            [
                'name' => 'middleName',
                'type' => Text::class,
                'options' => [
                    'label' => _('txt-middle-name'),
                ],
                'attributes' => [
                    'placeholder' => _('txt-give-your-middle-name'),
                ],
            ]
        );
        $this->add(
            [
                'name' => 'lastName',
                'type' => Text::class,
                'options' => [
                    'label' => _('txt-last-name'),
                ],
                'attributes' => [
                    'placeholder' => _('txt-give-your-last-name'),
                ],
            ]
        );
        $this->add(
            [
                'name' => 'email',
                'type' => Email::class,
                'options' => [
                    'label' => _('txt-company-email-address'),
                ],
                'attributes' => [
                    'placeholder' => _('txt-give-your-company-email-address'),
                ],
            ]
        );
        $this->add(
            [
                'type' => EntityMultiCheckbox::class,
                'name' => 'optIn',
                'options' => [
                    'target_class' => OptIn::class,
                    'object_manager' => $this->entityManager,
                    'find_method' => [
                        'name' => 'findBy',
                        'params' => [
                            'criteria' => [
                                'active' => OptIn::ACTIVE_ACTIVE,
                            ],
                            'orderBy' => [
                                'optIn' => Criteria::ASC,
                            ],
                        ],
                    ],
                    'label_generator' => static fn(OptIn $optIn) => sprintf(
                        '%s (%s)',
                        $optIn->getOptIn(),
                        $optIn->getDescription()
                    ),
                    'label' => _('txt-select-your-opt-in-label'),
                    'help-block' => _('txt-select-your-opt-in-help-block'),
                ],
            ]
        );
        $this->add(
            [
                'name' => 'g-recaptcha-response',
                'type' => Recaptcha::class,
            ]
        );
        $this->add(
            [
                'name' => 'csrf',
                'type' => Csrf::class,
            ]
        );
        $this->add(
            [
                'name' => 'submit',
                'type' => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-register'),
                ],
            ]
        );
    }
}
