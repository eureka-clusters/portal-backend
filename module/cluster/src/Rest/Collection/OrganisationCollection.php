<?php

declare(strict_types=1);

namespace Cluster\Rest\Collection;

use Cluster\Entity;
use Cluster\Provider\OrganisationProvider;
use JetBrains\PhpStorm\Deprecated;
use JetBrains\PhpStorm\Pure;
use Laminas\Paginator\Adapter\ArrayAdapter;

#[Deprecated]
final class OrganisationCollection extends ArrayAdapter
{
    #[Pure] public function __construct(array $array, private OrganisationProvider $organisationProvider)
    {
        parent::__construct($array);
    }

    public function getItems($offset, $itemCountPerPage): array
    {
        $set = parent::getItems($offset, $itemCountPerPage);

        $collection = [];
        /** @var Entity\Organisation $organisation */
        foreach ($set as $organisation) {
            $collection[] = $this->organisationProvider->generateArray($organisation);
        }

        return $collection;
    }
}
