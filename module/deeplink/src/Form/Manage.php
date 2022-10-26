<?php

declare(strict_types=1);

namespace Deeplink\Form;

use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;

use function _;

final class Manage extends Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setAttribute(key: 'method', value: 'post');
        $this->setAttribute(key: 'action', value: '');
        $this->add(
            elementOrFieldset: [
                'type' => Csrf::class,
                'name' => 'csrf',
            ]
        );
        $this->add(
            elementOrFieldset: [
                'type'       => Submit::class,
                'name'       => 'deleteInactiveDeeplinks',
                'attributes' => [
                    'class' => 'btn btn-warning',
                    'value' => _('txt-delete-inactive-deeplinks'),
                ],
            ]
        );

        $this->add(
            elementOrFieldset: [
                'type'       => Submit::class,
                'name'       => 'deleteTargets',
                'attributes' => [
                    'class' => 'btn btn-warning',
                    'value' => _('txt-delete-selected-targets'),
                ],
            ]
        );
    }
}
