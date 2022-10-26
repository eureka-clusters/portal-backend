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
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');
        $this->add(
            [
                'type' => Csrf::class,
                'name' => 'csrf',
            ]
        );
        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'deleteInactiveDeeplinks',
                'attributes' => [
                    'class' => 'btn btn-warning',
                    'value' => _('txt-delete-inactive-deeplinks'),
                ],
            ]
        );

        $this->add(
            [
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
