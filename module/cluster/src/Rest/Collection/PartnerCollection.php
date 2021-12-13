<?php

declare(strict_types=1);

namespace Cluster\Rest\Collection;

use Cluster\Entity;
use Cluster\Provider\Project\PartnerProvider;
use JetBrains\PhpStorm\Pure;
use Laminas\Paginator\Adapter\ArrayAdapter;

final class PartnerCollection extends ArrayAdapter
{
    private PartnerProvider $partnerProvider;

    #[Pure] public function __construct(array $array, PartnerProvider $partnerProvider)
    {
        parent::__construct($array);

        $this->partnerProvider = $partnerProvider;
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
