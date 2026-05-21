<?php

declare(strict_types=1);

namespace Cluster\Export;

use Cluster\Entity\Cluster;
use Cluster\Export\Cluster\GroupColumns;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class ClusterColumns extends AbstractEntityColumns
{
    protected string  $name        = 'cluster_cluster';
    protected string  $entity      = Cluster::class;
    protected ?string $description = 'This export contains all clusters. Use the Id column to resolve cluster references from other exports, such as the PrimaryClusterId and SecondaryClusterId columns in cluster_project.';

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn          = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false, description: 'The unique identifier of the cluster');
        $nameColumn        = new Column(columnName: 'Name', isNullable: false, description: 'The name of the cluster');
        $identifierColumn  = new Column(columnName: 'Identifier', isNullable: false, description: 'The internal identifier of the cluster');
        $descriptionColumn = new Column(columnName: 'Description', description: 'The description of the cluster');
        $dateCreatedColumn = new Column(columnName: 'DateCreated', isNullable: false, description: 'The date and time when the cluster record was created');
        $dateUpdatedColumn = new Column(columnName: 'DateUpdated', description: 'The date and time when the cluster record was last updated');

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

    #[\Override]
    public function getDependencies(): array
    {
        return [
            GroupColumns::class,
        ];
    }
}
