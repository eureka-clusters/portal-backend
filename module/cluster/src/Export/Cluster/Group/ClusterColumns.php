<?php

declare(strict_types=1);

namespace Cluster\Export\Cluster\Group;

use Cluster\Entity\Cluster\Group;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class ClusterColumns extends AbstractEntityColumns
{
    protected string $name = 'cluster_cluster_group_cluster';

    protected string $entity = Group::class;

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $groupIdColumn   = new Column(columnName: 'GroupId', type: Column::TYPE_INTEGER, isNullable: false, description: 'Cluster group identifier, links to cluster_cluster_group.Id');
        $clusterIdColumn = new Column(columnName: 'ClusterId', type: Column::TYPE_INTEGER, isNullable: false, description: 'Cluster identifier, links to cluster_cluster.Id');

        $amount = $this->findCount(criteria: []);
        $i      = 0;

        while ($i < $amount) {
            $elements = $this->findSliced(offset: $i, criteria: []);

            /** @var Group $group */
            foreach ($elements as $group) {
                foreach ($group->getClusters() as $cluster) {
                    $groupIdColumn->addRow($group->getId());
                    $clusterIdColumn->addRow($cluster->getId());
                }
            }

            //clear the entity manager to prevent piling up entities
            $this->entityManager->clear();

            $i += $this->chunkSize;
        }

        return [
            $groupIdColumn,
            $clusterIdColumn,
        ];
    }
}
