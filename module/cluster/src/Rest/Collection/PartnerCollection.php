<?php

declare(strict_types=1);

namespace Cluster\Rest\Collection;

use Cluster\Entity;
use Cluster\Provider\Project\PartnerProvider;
use JetBrains\PhpStorm\Pure;
use Laminas\Paginator\Adapter\ArrayAdapter;

final class PartnerCollection extends ArrayAdapter
{
    #[Pure] public function __construct(array $array, private PartnerProvider $partnerProvider)
    {
        parent::__construct($array);
    }

    public function getItems($offset, $itemCountPerPage): array
    {
        $set = parent::getItems($offset, $itemCountPerPage);

        $collection = [];
        /** @var Entity\Project\Partner $partner */
        foreach ($set as $partner) {
            $collection[] = $this->partnerProvider->generateArray($partner);
        }

        return $collection;
    }
}
