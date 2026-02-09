<?php

declare(strict_types=1);

namespace Cluster\Export\Project\Version;

use Cluster\Entity\Project\Version\CostsAndEffort;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class CostsAndEffortColumns extends AbstractEntityColumns
{
    protected string $name = 'cluster_project_version_costs_and_effort';

    protected string $entity = CostsAndEffort::class;

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn        = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false);
        $partnerIdColumn = new Column(columnName: 'PartnerId', type: Column::TYPE_INTEGER, isNullable: false);
        $versionIdColumn = new Column(columnName: 'VersionId', type: Column::TYPE_INTEGER, isNullable: false);
        $yearColumn      = new Column(columnName: 'Year', type: Column::TYPE_INTEGER, isNullable: false);
        $effortColumn    = new Column(columnName: 'Effort', type: Column::TYPE_FLOAT, isNullable: false);
        $costsColumn     = new Column(columnName: 'Costs', type: Column::TYPE_FLOAT, isNullable: false);

        $amount = $this->findCount(criteria: []);
        $i      = 0;

        while ($i < $amount) {
            $elements = $this->findSliced(offset: $i, criteria: []);

            /** @var CostsAndEffort $costsAndEffort */
            foreach ($elements as $costsAndEffort) {
                $idColumn->addRow($costsAndEffort->getId());
                $partnerIdColumn->addRow($costsAndEffort->getPartner()->getId());
                $versionIdColumn->addRow($costsAndEffort->getVersion()->getId());
                $yearColumn->addRow($costsAndEffort->getYear());
                $effortColumn->addRow($costsAndEffort->getEffort());
                $costsColumn->addRow($costsAndEffort->getCosts());
            }

            //clear the entity manager to prevent piling up entities
            $this->entityManager->clear();

            $i += $this->chunkSize;
        }

        return [
            $idColumn,
            $partnerIdColumn,
            $versionIdColumn,
            $yearColumn,
            $effortColumn,
            $costsColumn,
        ];
    }
}
