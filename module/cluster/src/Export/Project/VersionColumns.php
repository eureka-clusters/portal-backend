<?php

declare(strict_types=1);

namespace Cluster\Export\Project;

use Cluster\Entity\Project\Version as ProjectVersion;
use Cluster\Export\Project\Version\CostsAndEffortColumns;
use Cluster\Export\Version\StatusColumns as VersionStatusColumns;
use Cluster\Export\Version\TypeColumns as VersionTypeColumns;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class VersionColumns extends AbstractEntityColumns
{
    protected string $name = 'cluster_project_version';

    protected string  $entity      = ProjectVersion::class;
    protected ?string $description = 'This export contains the versions of projects. ProjectId links to cluster_project, TypeId links to cluster_version_type, StatusId links to cluster_version_type, and Countries is exported as a JSON array.';

    /**
     * @return array<Column>
     * @throws \JsonException
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn             = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false, description: 'The unique identifier of the project version');
        $identifierColumn     = new Column(columnName: 'Identifier', isNullable: false, description: 'The unique identifier string of the project version');
        $projectIdColumn      = new Column(columnName: 'ProjectId', type: Column::TYPE_INTEGER, isNullable: false, description: 'The id of the related project, which links to cluster_project');
        $typeIdColumn         = new Column(columnName: 'TypeId', type: Column::TYPE_INTEGER, isNullable: false, description: 'The id of the version type, which links to cluster_version_type');
        $submissionDateColumn = new Column(columnName: 'SubmissionDate', type: Column::TYPE_DATE, description: 'The submission date of the project version');
        $reviewDateColumn     = new Column(columnName: 'ReviewDate', type: Column::TYPE_DATE, description: 'The review date of the project version');
        $statusIdColumn       = new Column(columnName: 'StatusId', type: Column::TYPE_INTEGER, isNullable: false, description: 'The id of the version status, which links to cluster_version_type');
        $costsColumn          = new Column(columnName: 'Costs', type: Column::TYPE_FLOAT, isNullable: false, description: 'The total costs recorded for the project version, in EUR');
        $effortColumn         = new Column(columnName: 'Effort', type: Column::TYPE_FLOAT, isNullable: false, description: 'The total effort recorded for the project version, in PY');
        $countriesColumn      = new Column(columnName: 'Countries', type: Column::TYPE_STRING, isNullable: true, description: 'The countries associated with the project version, encoded as JSON');

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

    #[\Override]
    public function getDependencies(): array
    {
        return [
            VersionTypeColumns::class,
            VersionStatusColumns::class,
            CostsAndEffortColumns::class
        ];
    }
}
