<?php

declare(strict_types=1);

namespace Cluster\Provider;

use Api\Provider\ProviderInterface;
use Application\ValueObject\SearchResult;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class SearchResultProvider implements ProviderInterface
{
    /**
     * @param SearchResult $entity
     */
    #[ArrayShape(shape: [
        'type'             => "string",
        'slug'             => "string",
        'name'             => "string",
        'title'            => "null|string",
        'description'      => "null|string",
        'organisationType' => "null|string",
        'country'          => "null|string",
    ])] #[Pure] public function generateArray($entity): array
    {
        return $entity->toArray();
    }
}
