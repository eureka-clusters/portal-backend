<?php

declare(strict_types=1);

namespace Cluster\Export;

use Cluster\Entity\Project;
use Cluster\Export\ClusterColumns;
use Cluster\Export\FunderColumns;
use Cluster\Export\Project\EvaluationColumns;
use Cluster\Export\Project\PartnerColumns;
use Cluster\Export\Project\StatusColumns;
use Cluster\Export\Project\VersionColumns;
use General\Export\DateColumns;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class ProjectColumns extends AbstractEntityColumns
{
    protected string $name = 'cluster_project';

    protected string $entity = Project::class;

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn                        = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false);
        $identifierColumn                = new Column(columnName: 'Identifier', isNullable: false);
        $slugColumn                      = new Column(columnName: 'Slug', isNullable: false);
        $dateCreatedColumn               = new Column(columnName: 'DateCreated', isNullable: false);
        $dateUpdatedColumn               = new Column(columnName: 'DateUpdated');
        $numberColumn                    = new Column(columnName: 'Number', isNullable: false);
        $nameColumn                      = new Column(columnName: 'Name', isNullable: false);
        $titleColumn                     = new Column(columnName: 'Title', isNullable: false);
        $descriptionColumn               = new Column(columnName: 'Description');
        $technicalAreaColumn             = new Column(columnName: 'TechnicalArea');
        $programmeColumn                 = new Column(columnName: 'Programme', isNullable: false);
        $programmeCallColumn             = new Column(columnName: 'ProgrammeCall', isNullable: false);
        $primaryClusterIdColumn          = new Column(columnName: 'PrimaryClusterId', type: Column::TYPE_INTEGER, isNullable: false);
        $secondaryClusterIdColumn        = new Column(columnName: 'SecondaryClusterId', type: Column::TYPE_INTEGER);
        $labelDateColumn                 = new Column(columnName: 'LabelDate', type: Column::TYPE_DATE);
        $cancelDateColumn                = new Column(columnName: 'CancelDate', type: Column::TYPE_DATE);
        $officialStartDateColumn         = new Column(columnName: 'OfficialStartDate', type: Column::TYPE_DATE);
        $officialEndDateColumn           = new Column(columnName: 'OfficialEndDate', type: Column::TYPE_DATE);
        $statusIdColumn                  = new Column(columnName: 'StatusId', type: Column::TYPE_INTEGER, isNullable: false);
        $projectLeaderColumn             = new Column(columnName: 'ProjectLeader', type: Column::TYPE_STRING, isNullable: true);
        $projectOutlineCostsColumn       = new Column(columnName: 'ProjectOutlineCosts', type: Column::TYPE_FLOAT);
        $projectOutlineEffortColumn      = new Column(columnName: 'ProjectOutlineEffort', type: Column::TYPE_FLOAT);
        $fullProjectProposalCostsColumn  = new Column(columnName: 'FullProjectProposalCosts', type: Column::TYPE_FLOAT, isNullable: false);
        $fullProjectProposalEffortColumn = new Column(columnName: 'FullProjectProposalEffort', type: Column::TYPE_FLOAT, isNullable: false);
        $latestVersionCostsColumn        = new Column(columnName: 'LatestVersionCosts', type: Column::TYPE_FLOAT, isNullable: false);
        $latestVersionEffortColumn       = new Column(columnName: 'LatestVersionEffort', type: Column::TYPE_FLOAT, isNullable: false);

        $amount = $this->findCount(criteria: []);
        $i      = 0;

        while ($i < $amount) {
            $elements = $this->findSliced(offset: $i, criteria: []);

            /** @var Project $project */
            foreach ($elements as $project) {
                $idColumn->addRow($project->getId());
                $identifierColumn->addRow($project->getIdentifier());
                $slugColumn->addRow($project->getSlug());
                $dateCreatedColumn->addRow($project->getDateCreated()->format('Y-m-d H:i:s'));
                $dateUpdatedColumn->addRow($project->getDateUpdated()?->format('Y-m-d H:i:s'));
                $numberColumn->addRow($project->getNumber());
                $nameColumn->addRow($project->getName());
                $titleColumn->addRow($project->getTitle());
                $descriptionColumn->addRow($project->getDescription());
                $technicalAreaColumn->addRow($project->getTechnicalArea());
                $programmeColumn->addRow($project->getProgramme());
                $programmeCallColumn->addRow($project->getProgrammeCall());
                $primaryClusterIdColumn->addRow($project->getPrimaryCluster()->getId());
                $secondaryClusterIdColumn->addRow($project->getSecondaryCluster()?->getId());
                $labelDateColumn->addRow($project->getLabelDate());
                $cancelDateColumn->addRow($project->getCancelDate());
                $officialStartDateColumn->addRow($project->getOfficialStartDate());
                $officialEndDateColumn->addRow($project->getOfficialEndDate());
                $statusIdColumn->addRow($project->getStatus()->getId());

                $projectLeaderJson = json_encode($project->getProjectLeader());
                $projectLeaderColumn->addRow($projectLeaderJson === false ? null : $projectLeaderJson);

                $projectOutlineCostsColumn->addRow($project->getProjectOutlineCosts());
                $projectOutlineEffortColumn->addRow($project->getProjectOutlineEffort());
                $fullProjectProposalCostsColumn->addRow($project->getFullProjectProposalCosts());
                $fullProjectProposalEffortColumn->addRow($project->getFullProjectProposalEffort());
                $latestVersionCostsColumn->addRow($project->getLatestVersionCosts());
                $latestVersionEffortColumn->addRow($project->getLatestVersionEffort());
            }

            //clear the entity manager to prevent piling up entities
            $this->entityManager->clear();

            $i += $this->chunkSize;
        }

        return [
            $idColumn,
            $identifierColumn,
            $slugColumn,
            $dateCreatedColumn,
            $dateUpdatedColumn,
            $numberColumn,
            $nameColumn,
            $titleColumn,
            $descriptionColumn,
            $technicalAreaColumn,
            $programmeColumn,
            $programmeCallColumn,
            $primaryClusterIdColumn,
            $secondaryClusterIdColumn,
            $labelDateColumn,
            $cancelDateColumn,
            $officialStartDateColumn,
            $officialEndDateColumn,
            $statusIdColumn,
            $projectLeaderColumn,
            $projectOutlineCostsColumn,
            $projectOutlineEffortColumn,
            $fullProjectProposalCostsColumn,
            $fullProjectProposalEffortColumn,
            $latestVersionCostsColumn,
            $latestVersionEffortColumn,
        ];
    }

    public function getDependencies(): array
    {
        return [
            ClusterColumns::class,
            StatusColumns::class,
            VersionColumns::class,
            PartnerColumns::class,
            EvaluationColumns::class,
            FunderColumns::class,
        ];
    }
}
