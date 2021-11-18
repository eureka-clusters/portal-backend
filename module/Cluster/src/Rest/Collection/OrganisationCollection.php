<?php

declare(strict_types=1);

namespace Cluster\Rest\Collection;

use Cluster\Entity;
use Cluster\Provider\OrganisationProvider;
use Laminas\Paginator\Adapter\ArrayAdapter;

final class OrganisationCollection extends ArrayAdapter
{
    private OrganisationProvider $organisationProvider;

    public function __construct(array $array, OrganisationProvider $organisationProvider)
    {
        parent::__construct($array);

        $this->organisationProvider = $organisationProvider;
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
