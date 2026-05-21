<?php

declare(strict_types=1);

namespace Cluster\Export\Project\Version;

use Cluster\Entity\Project\Version\CostsAndEffort;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class CostsAndEffortColumns extends AbstractEntityColumns
{
    protected string $name = 'cluster_project_version_costs_and_effort';

    protected string  $entity      = CostsAndEffort::class;
    protected ?string $description = 'This export contains the yearly costs and effort breakdown per project version and partner. PartnerId links to cluster_project_partner and VersionId links to cluster_project_version.';

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn        = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false, description: 'The unique identifier of the costs and effort record');
        $partnerIdColumn = new Column(columnName: 'PartnerId', type: Column::TYPE_INTEGER, isNullable: false, description: 'The id of the related project partner, which links to cluster_project_partner');
        $versionIdColumn = new Column(columnName: 'VersionId', type: Column::TYPE_INTEGER, isNullable: false, description: 'The id of the related project version, which links to cluster_project_version');
        $yearColumn      = new Column(columnName: 'Year', type: Column::TYPE_INTEGER, isNullable: false, description: 'The year for this costs and effort entry');
        $effortColumn    = new Column(columnName: 'Effort', type: Column::TYPE_FLOAT, isNullable: false, description: 'The effort value for this year, partner, and project version, in PY');
        $costsColumn     = new Column(columnName: 'Costs', type: Column::TYPE_FLOAT, isNullable: false, description: 'The costs value for this year, partner, and project version, in EUR');

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
