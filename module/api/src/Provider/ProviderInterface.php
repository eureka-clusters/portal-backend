<?php

declare(strict_types=1);

namespace Api\Provider;

interface ProviderInterface
{
    public function generateArray($entity): array;
}