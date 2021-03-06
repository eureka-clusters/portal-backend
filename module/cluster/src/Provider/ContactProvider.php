<?php

declare(strict_types=1);

namespace Cluster\Provider;

use Api\Provider\ProviderInterface;

class ContactProvider implements ProviderInterface
{
    /**
     * @param array $contact
     * @return array
     */
    public function generateArray($contact): array
    {
        return $contact;
    }
}
