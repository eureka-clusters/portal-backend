<?php

declare(strict_types=1);

namespace Cluster\Form;

use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use function _;

class ProjectManipulation extends Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setAttribute(key: 'method', value: 'post');

        $this->add(
            elementOrFieldset: [
                'type' => Csrf::class,
                'name' => 'csrf',
            ]
        );

        $this->add(
            elementOrFieldset: [
                'type'       => Submit::class,
                'name'       => 'delete',
                'attributes' => [
                    'class' => 'btn btn-danger',
                    'value' => _('txt-delete'),
                ],
            ]
        );
    }
}
