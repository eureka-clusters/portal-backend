<?php

declare(strict_types=1);

namespace Application;

use Application\Event\SetTitle;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use ZfcTwig\View\TwigRenderer;

return [
    ConfigAbstractFactory::class => [

        SetTitle::class => [
            TwigRenderer::class
        ],
    ],
];
