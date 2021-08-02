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
use Cluster\Provider\PartnerProvider;
use Laminas\Paginator\Adapter\ArrayAdapter;

/**
 * Class PartnerCollection
 * @package Partner\Rest\Collection
 */
final class PartnerCollection extends ArrayAdapter
{
    private PartnerProvider $partnerProvider;

    public function __construct(array $array, PartnerProvider $partnerProvider)
    {
        parent::__construct($array);

        $this->partnerProvider = $partnerProvider;
    }

    public function getItems($offset, $itemCountPerPage): array
    {
        $set = parent::getItems($offset, $itemCountPerPage);

        $collection = [];
        /** @var Entity\Statistics\Partner $partner */
        foreach ($set as $partner) {
            $collection[] = $this->partnerProvider->generateArray($partner);
        }

        return $collection;
    }
}
