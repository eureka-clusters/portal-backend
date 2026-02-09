<?php

declare(strict_types=1);

namespace Cluster\Export;

use Cluster\Entity\Cluster;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class ClusterColumns extends AbstractEntityColumns
{
    protected string $name = 'cluster_cluster';

    protected string $entity = Cluster::class;

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn          = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false);
        $nameColumn        = new Column(columnName: 'Name', isNullable: false);
        $identifierColumn  = new Column(columnName: 'Identifier', isNullable: false);
        $descriptionColumn = new Column(columnName: 'Description');
        $dateCreatedColumn = new Column(columnName: 'DateCreated', isNullable: false);
        $dateUpdatedColumn = new Column(columnName: 'DateUpdated');

        $amount = $this->findCount(criteria: []);
        $i      = 0;

        while ($i < $amount) {
            $elements = $this->findSliced(offset: $i, criteria: []);

            /** @var Cluster $cluster */
            foreach ($elements as $cluster) {
                $idColumn->addRow($cluster->getId());
                $nameColumn->addRow($cluster->getName());
                $identifierColumn->addRow($cluster->getIdentifier());
                $descriptionColumn->addRow($cluster->getDescription());
                $dateCreatedColumn->addRow($cluster->getDateCreated()->format('Y-m-d H:i:s'));
                $dateUpdatedColumn->addRow($cluster->getDateUpdated()?->format('Y-m-d H:i:s'));
            }

            //clear the entity manager to prevent piling up entities
            $this->entityManager->clear();

            $i += $this->chunkSize;
        }

        return [
            $idColumn,
            $nameColumn,
            $identifierColumn,
            $descriptionColumn,
            $dateCreatedColumn,
            $dateUpdatedColumn,
        ];
    }
}
