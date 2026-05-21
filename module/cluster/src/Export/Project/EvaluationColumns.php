<?php

declare(strict_types=1);

namespace Cluster\Export\Project;

use Cluster\Entity\Project\Evaluation;
use Cluster\Export\CountryColumns;
use Cluster\Export\Funding\StatusColumns as FundingStatusColumns;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class EvaluationColumns extends AbstractEntityColumns
{
    protected string $name = 'cluster_project_evaluation';

    protected string  $entity      = Evaluation::class;
    protected ?string $description = 'This export contains project evaluation records. StatusId links to cluster_funding_status, CountryId links to cluster_country, ProjectId links to cluster_project, and ProjectVersionId links to cluster_project_version when the evaluation applies to a specific project version.';

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn               = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false, description: 'The unique identifier of the evaluation record');
        $descriptionColumn      = new Column(columnName: 'Description', isNullable: false, description: 'The text description of the evaluation');
        $dateCreatedColumn      = new Column(columnName: 'DateCreated', isNullable: false, description: 'The date and time when the evaluation was created');
        $statusIdColumn         = new Column(columnName: 'StatusId', type: Column::TYPE_INTEGER, isNullable: false, description: 'The id of the funding status for this evaluation, which links to cluster_funding_status');
        $userIdColumn           = new Column(columnName: 'UserId', type: Column::TYPE_INTEGER, isNullable: false, description: 'The id of the user who created or owns the evaluation');
        $countryIdColumn        = new Column(columnName: 'CountryId', type: Column::TYPE_INTEGER, isNullable: false, description: 'The id of the country for this evaluation, which links to cluster_country');
        $projectIdColumn        = new Column(columnName: 'ProjectId', type: Column::TYPE_INTEGER, isNullable: false, description: 'The id of the related project, which links to cluster_project');
        $projectVersionIdColumn = new Column(columnName: 'ProjectVersionId', type: Column::TYPE_INTEGER, description: 'The id of the related project version, which links to cluster_project_version; this is empty for a project-level funding status');

        $amount = $this->findCount(criteria: []);
        $i      = 0;

        while ($i < $amount) {
            $elements = $this->findSliced(offset: $i, criteria: []);

            /** @var Evaluation $evaluation */
            foreach ($elements as $evaluation) {
                $idColumn->addRow($evaluation->getId());
                $descriptionColumn->addRow($evaluation->getDescription());
                $dateCreatedColumn->addRow($evaluation->getDateCreated()->format('Y-m-d H:i:s'));
                $statusIdColumn->addRow($evaluation->getStatus()->getId());
                $userIdColumn->addRow($evaluation->getUser()->getId());
                $countryIdColumn->addRow($evaluation->getCountry()->getId());
                $projectIdColumn->addRow($evaluation->getProject()->getId());
                $projectVersionIdColumn->addRow($evaluation->getProjectVersion()?->getId());
            }

            //clear the entity manager to prevent piling up entities
            $this->entityManager->clear();

            $i += $this->chunkSize;
        }

        return [
            $idColumn,
            $descriptionColumn,
            $dateCreatedColumn,
            $statusIdColumn,
            $userIdColumn,
            $countryIdColumn,
            $projectIdColumn,
            $projectVersionIdColumn,
        ];
    }

    #[\Override]
    public function getDependencies(): array
    {
        return [
            FundingStatusColumns::class,
            CountryColumns::class,
            VersionColumns::class,
        ];
    }
}
