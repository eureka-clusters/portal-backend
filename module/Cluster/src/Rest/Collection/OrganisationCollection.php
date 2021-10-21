<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Rest\Collection;

use Cluster\Entity;
use Cluster\Provider\OrganisationProvider;
use Laminas\Paginator\Adapter\ArrayAdapter;

/**
 * Class PartnerCollection
 * @package Organisation\Rest\Collection
 */
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
