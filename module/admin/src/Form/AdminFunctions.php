<?php

declare(strict_types=1);

namespace Admin\Form;

use Laminas\Form\Element\Csrf;use Laminas\Form\Element\Submit;use Laminas\Form\Form;use function _;

final class AdminFunctions extends Form
{
    public const ACTION_CLEANUP_FILE_CACHE = 'cleanup-file-cache';
    public const ACTION_FLUSH_REDIS_CACHE = 'flush-redis-cache';

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
                'type' => Submit::class,
                'name' => self::ACTION_CLEANUP_FILE_CACHE,
                'attributes' => [
                    'class' => "btn btn-danger",
                    'value' => _("txt-cleanup-file-cache"),
                ],
            ]
        );

        $this->add(
            [
                'type' => Submit::class,
                'name' => self::ACTION_FLUSH_REDIS_CACHE,
                'attributes' => [
                    'class' => "btn btn-danger",
                    'value' => _("txt-flush-redis-cache"),
                ],
            ]
        );
    }
}
