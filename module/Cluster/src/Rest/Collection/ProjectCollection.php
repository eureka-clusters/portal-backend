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
use Cluster\Provider\ProjectProvider;
use Laminas\Paginator\Adapter\ArrayAdapter;

/**
 * Class ProjectCollection
 * @package Project\Rest\Collection
 */
final class ProjectCollection extends ArrayAdapter
{
    private ProjectProvider $projectProvider;

    public function __construct(array $array, ProjectProvider $projectProvider)
    {
        parent::__construct($array);

        $this->projectProvider = $projectProvider;
    }

    public function getItems($offset, $itemCountPerPage): array
    {
        $set = parent::getItems($offset, $itemCountPerPage);

        $collection = [];
        /** @var Entity\Statistics\Partner $project */
        foreach ($set as $project) {
            $collection[] = $this->projectProvider->generateArray($project);
        }

        return $collection;
    }
}
