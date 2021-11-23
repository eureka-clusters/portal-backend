<?php

declare(strict_types=1);

namespace Cluster\Rest\Collection;

use Cluster\Entity;
use Cluster\Provider\ProjectProvider;
use Laminas\Paginator\Adapter\ArrayAdapter;

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
        /** @var Entity\Project $project */
        foreach ($set as $project) {
            $collection[] = $this->projectProvider->generateArray($project);
        }

        return $collection;
    }
}
