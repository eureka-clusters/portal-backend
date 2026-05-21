<?php

declare(strict_types=1);

namespace Cluster\Export;

use Cluster\Entity\Project;
use Cluster\Export\Project\AreaColumns;
use Cluster\Export\Project\EvaluationColumns;
use Cluster\Export\Project\PartnerColumns;
use Cluster\Export\Project\StatusColumns;
use Cluster\Export\Project\VersionColumns;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class ProjectColumns extends AbstractEntityColumns
{
    protected string $name = 'cluster_project';

    protected string  $entity      = Project::class;
    protected ?string $description = 'This export contains all projects and their main metadata. PrimaryClusterId and SecondaryClusterId link to cluster_cluster, StatusId links to the project status export, and ProjectLeader is exported as a JSON string with the project leader details.';

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn                        = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false, description: 'The unique identifier of the project');
        $identifierColumn                = new Column(columnName: 'Identifier', isNullable: false, description: 'The unique identifier string of the project');
        $slugColumn                      = new Column(columnName: 'Slug', isNullable: false, description: 'The URL-friendly identifier generated from the project name');
        $dateCreatedColumn               = new Column(columnName: 'DateCreated', type: Column::TYPE_DATE, isNullable: false, description: 'The date when the project record was created');
        $dateUpdatedColumn               = new Column(columnName: 'DateUpdated', type: Column::TYPE_DATE, description: 'The date when the project record was last updated');
        $numberColumn                    = new Column(columnName: 'Number', isNullable: false, description: 'The project reference number');
        $nameColumn                      = new Column(columnName: 'Name', isNullable: false, description: 'The name of the project');
        $titleColumn                     = new Column(columnName: 'Title', isNullable: false, description: 'The title of the project');
        $descriptionColumn               = new Column(columnName: 'Description', description: 'The detailed description of the project');
        $technicalAreaColumn             = new Column(columnName: 'TechnicalArea', description: 'The technical area assigned to the project');
        $programmeColumn                 = new Column(columnName: 'Programme', isNullable: false, description: 'The programme under which the project is submitted');
        $programmeCallColumn             = new Column(columnName: 'ProgrammeCall', isNullable: false, description: 'The programme call associated with the project');
        $programmeCallPoOpenDateColumn   = new Column(columnName: 'ProgrammeCallPoOpenDate', type: Column::TYPE_DATE, isNullable: true, description: 'The opening date of the project outline call');
        $programmeCallPoCloseDateColumn  = new Column(columnName: 'ProgrammeCallPoCloseDate', type: Column::TYPE_DATE, isNullable: true, description: 'The closing date of the project outline call');
        $programmeCallFppOpenDateColumn  = new Column(columnName: 'ProgrammeCallFppOpenDate', type: Column::TYPE_DATE, isNullable: false, description: 'The opening date of the full project proposal call');
        $programmeCallFppCloseDateColumn = new Column(columnName: 'ProgrammeCallFppCloseDate', type: Column::TYPE_DATE, isNullable: false, description: 'The closing date of the full project proposal call');
        $primaryClusterIdColumn          = new Column(columnName: 'PrimaryClusterId', type: Column::TYPE_INTEGER, isNullable: false, description: 'The id of the primary cluster, which links to cluster_cluster');
        $secondaryClusterIdColumn        = new Column(columnName: 'SecondaryClusterId', type: Column::TYPE_INTEGER, description: 'The id of the secondary cluster, which links to cluster_cluster');
        $labelDateColumn                 = new Column(columnName: 'LabelDate', type: Column::TYPE_DATE, description: 'The date when the project received its label');
        $cancelDateColumn                = new Column(columnName: 'CancelDate', type: Column::TYPE_DATE, description: 'The date when the project was cancelled');
        $officialStartDateColumn         = new Column(columnName: 'OfficialStartDate', type: Column::TYPE_DATE, description: 'The official start date of the project');
        $officialEndDateColumn           = new Column(columnName: 'OfficialEndDate', type: Column::TYPE_DATE, description: 'The official end date of the project');
        $isSuccessfulColumn              = new Column(columnName: 'isSuccessful', type: Column::TYPE_BOOLEAN, isNullable: false, description: 'Whether the project is marked as successful');
        $statusIdColumn                  = new Column(columnName: 'StatusId', type: Column::TYPE_INTEGER, isNullable: false, description: 'The id of the project status, which links to the cluster_project_partner');
        $projectLeaderColumn             = new Column(columnName: 'ProjectLeader', type: Column::TYPE_STRING, isNullable: true, description: 'The project leader details encoded as JSON');
        $projectOutlineCostsColumn       = new Column(columnName: 'ProjectOutlineCosts', type: Column::TYPE_FLOAT, description: 'The total project outline costs');
        $projectOutlineEffortColumn      = new Column(columnName: 'ProjectOutlineEffort', type: Column::TYPE_FLOAT, description: 'The total project outline effort');
        $fullProjectProposalCostsColumn  = new Column(columnName: 'FullProjectProposalCosts', type: Column::TYPE_FLOAT, isNullable: false, description: 'The total costs in the full project proposal');
        $fullProjectProposalEffortColumn = new Column(columnName: 'FullProjectProposalEffort', type: Column::TYPE_FLOAT, isNullable: false, description: 'The total effort in the full project proposal');
        $latestVersionCostsColumn        = new Column(columnName: 'LatestVersionCosts', type: Column::TYPE_FLOAT, isNullable: false, description: 'The total costs in the latest project version');
        $latestVersionEffortColumn       = new Column(columnName: 'LatestVersionEffort', type: Column::TYPE_FLOAT, isNullable: false, description: 'The total effort in the latest project version');

        $amount = $this->findCount(criteria: []);
        $i      = 0;

        while ($i < $amount) {
            $elements = $this->findSliced(offset: $i, criteria: []);

            /** @var Project $project */
            foreach ($elements as $project) {
                $idColumn->addRow($project->getId());
                $identifierColumn->addRow($project->getIdentifier());
                $slugColumn->addRow($project->getSlug());
                $dateCreatedColumn->addRow($project->getDateCreated());
                $dateUpdatedColumn->addRow($project->getDateUpdated());
                $numberColumn->addRow($project->getNumber());
                $nameColumn->addRow($project->getName());
                $titleColumn->addRow($project->getTitle());
                $descriptionColumn->addRow($project->getDescription());
                $technicalAreaColumn->addRow($project->getTechnicalArea());
                $programmeColumn->addRow($project->getProgramme());
                $programmeCallColumn->addRow($project->getProgrammeCall());
                $programmeCallPoOpenDateColumn->addRow($project->getProgramCallPoOpenDate());
                $programmeCallPoCloseDateColumn->addRow($project->getProgramCallPoCloseDate());
                $programmeCallFppOpenDateColumn->addRow($project->getProgramCallFppOpenDate());
                $programmeCallFppCloseDateColumn->addRow($project->getProgramCallFppCloseDate());

                $primaryClusterIdColumn->addRow($project->getPrimaryCluster()->getId());
                $secondaryClusterIdColumn->addRow($project->getSecondaryCluster()?->getId());
                $labelDateColumn->addRow($project->getLabelDate());
                $cancelDateColumn->addRow($project->getCancelDate());
                $officialStartDateColumn->addRow($project->getOfficialStartDate());
                $officialEndDateColumn->addRow($project->getOfficialEndDate());
                $isSuccessfulColumn->addRow($project->isSuccessful());
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
            $programmeCallPoOpenDateColumn,
            $programmeCallPoCloseDateColumn,
            $programmeCallFppOpenDateColumn,
            $programmeCallFppCloseDateColumn,
            $primaryClusterIdColumn,
            $secondaryClusterIdColumn,
            $labelDateColumn,
            $cancelDateColumn,
            $officialStartDateColumn,
            $officialEndDateColumn,
            $isSuccessfulColumn,
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

    #[\Override]
    public function getDependencies(): array
    {
        return [
            ClusterColumns::class,
            StatusColumns::class,
            VersionColumns::class,
            PartnerColumns::class,
            EvaluationColumns::class,
            FunderColumns::class,
            AreaColumns::class
        ];
    }
}
