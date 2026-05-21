<?php

declare(strict_types=1);

namespace Cluster\Export\Cluster;

use Cluster\Entity\Cluster\Group;
use Cluster\Export\Cluster\Group\ClusterColumns;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class GroupColumns extends AbstractEntityColumns
{
    protected string $name = 'cluster_cluster_group';

    protected string $entity = Group::class;

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn          = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false);
        $nameColumn        = new Column(columnName: 'Name', isNullable: false);
        $descriptionColumn = new Column(columnName: 'Description');
        $dateCreatedColumn = new Column(columnName: 'DateCreated', isNullable: false);
        $dateUpdatedColumn = new Column(columnName: 'DateUpdated');

        $amount = $this->findCount(criteria: []);
        $i      = 0;

        while ($i < $amount) {
            $elements = $this->findSliced(offset: $i, criteria: []);

            /** @var Group $group */
            foreach ($elements as $group) {
                $idColumn->addRow($group->getId());
                $nameColumn->addRow($group->getName());
                $descriptionColumn->addRow($group->getDescription());
                $dateCreatedColumn->addRow($group->getDateCreated()->format('Y-m-d H:i:s'));
                $dateUpdatedColumn->addRow($group->getDateUpdated()?->format('Y-m-d H:i:s'));
            }

            //clear the entity manager to prevent piling up entities
            $this->entityManager->clear();

            $i += $this->chunkSize;
        }

        return [
            $idColumn,
            $nameColumn,
            $descriptionColumn,
            $dateCreatedColumn,
            $dateUpdatedColumn,
        ];
    }

    public function getDependencies(): array
    {
        return [
            ClusterColumns::class
        ];
    }
}
