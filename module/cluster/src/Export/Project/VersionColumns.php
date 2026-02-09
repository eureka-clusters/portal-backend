<?php

declare(strict_types=1);

namespace Cluster\Export\Project;

use Cluster\Entity\Project\Version as ProjectVersion;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class VersionColumns extends AbstractEntityColumns
{
    protected string $name = 'cluster_project_version';

    protected string $entity = ProjectVersion::class;

    /**
     * @return array<Column>
     * @throws \JsonException
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn             = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false);
        $identifierColumn     = new Column(columnName: 'Identifier', isNullable: false);
        $projectIdColumn      = new Column(columnName: 'ProjectId', type: Column::TYPE_INTEGER, isNullable: false);
        $typeIdColumn         = new Column(columnName: 'TypeId', type: Column::TYPE_INTEGER, isNullable: false);
        $submissionDateColumn = new Column(columnName: 'SubmissionDate', type: Column::TYPE_DATE);
        $reviewDateColumn     = new Column(columnName: 'ReviewDate', type: Column::TYPE_DATE);
        $statusIdColumn       = new Column(columnName: 'StatusId', type: Column::TYPE_INTEGER, isNullable: false);
        $costsColumn          = new Column(columnName: 'Costs', type: Column::TYPE_FLOAT, isNullable: false);
        $effortColumn         = new Column(columnName: 'Effort', type: Column::TYPE_FLOAT, isNullable: false);
        $countriesColumn      = new Column(columnName: 'Countries', type: Column::TYPE_STRING, isNullable: true);

        $amount = $this->findCount(criteria: []);
        $i      = 0;

        while ($i < $amount) {
            $elements = $this->findSliced(offset: $i, criteria: []);

            /** @var ProjectVersion $version */
            foreach ($elements as $version) {
                $idColumn->addRow($version->getId());
                $identifierColumn->addRow($version->getIdentifier());
                $projectIdColumn->addRow($version->getProject()->getId());
                $typeIdColumn->addRow($version->getType()->getId());
                $submissionDateColumn->addRow($version->getSubmissionDate());
                $reviewDateColumn->addRow($version->getReviewDate());
                $statusIdColumn->addRow($version->getStatus()->getId());
                $costsColumn->addRow($version->getCosts());
                $effortColumn->addRow($version->getEffort());

                $countriesJson = json_encode($version->getCountries(), JSON_THROW_ON_ERROR);
                $countriesColumn->addRow($countriesJson === false ? null : $countriesJson);
            }

            //clear the entity manager to prevent piling up entities
            $this->entityManager->clear();

            $i += $this->chunkSize;
        }

        return [
            $idColumn,
            $identifierColumn,
            $projectIdColumn,
            $typeIdColumn,
            $submissionDateColumn,
            $reviewDateColumn,
            $statusIdColumn,
            $costsColumn,
            $effortColumn,
            $countriesColumn,
        ];
    }
}
