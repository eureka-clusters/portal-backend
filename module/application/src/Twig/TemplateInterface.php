<?php

declare(strict_types=1);

namespace Application\Twig;

use DateTime;

interface TemplateInterface
{
    public function parseSourceContent(): string;

    public function parseName(): string;

    public function getLastUpdate(): ?DateTime;
}
