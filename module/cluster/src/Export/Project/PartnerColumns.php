<?php

declare(strict_types=1);

namespace Cluster\Export\Project;

use Cluster\Entity\Project\Partner;
use Cluster\Export\OrganisationColumns;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class PartnerColumns extends AbstractEntityColumns
{
    protected string $name = 'cluster_project_partner';

    protected string $entity = Partner::class;
    protected ?string $description = 'This export contains the partner records for projects. OrganisationId links to cluster_organisation, ProjectId links to cluster_project, and TechnicalContact is exported as a JSON string.';

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn                        = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false, description: 'The unique identifier of the project partner record');
        $organisationIdColumn            = new Column(columnName: 'OrganisationId', type: Column::TYPE_INTEGER, isNullable: false, description: 'The id of the linked organisation, which links to cluster_organisation');
        $projectIdColumn                 = new Column(columnName: 'ProjectId', type: Column::TYPE_INTEGER, isNullable: false, description: 'The id of the linked project, which links to cluster_project');
        $slugColumn                      = new Column(columnName: 'Slug', isNullable: false, description: 'The URL-friendly identifier generated for the partner record');
        $organisationNameColumn          = new Column(columnName: 'OrganisationName', isNullable: false, description: 'The name of the linked organisation');
        $projectNameColumn               = new Column(columnName: 'ProjectName', isNullable: false, description: 'The name of the linked project');
        $isActiveColumn                  = new Column(columnName: 'IsActive', type: Column::TYPE_BOOLEAN, isNullable: false, description: 'Whether the partner record is active');
        $isCoordinatorColumn             = new Column(columnName: 'IsCoordinator', type: Column::TYPE_BOOLEAN, isNullable: false, description: 'Whether this partner is the coordinator for the project');
        $isSelfFundedColumn              = new Column(columnName: 'IsSelfFunded', type: Column::TYPE_BOOLEAN, isNullable: false, description: 'Whether this partner is marked as self-funded');
        $technicalContactColumn          = new Column(columnName: 'TechnicalContact', description: 'The technical contact details encoded as JSON');
        $projectOutlineCostsColumn       = new Column(columnName: 'ProjectOutlineCosts', type: Column::TYPE_FLOAT, description: 'The partner costs in the project outline phase');
        $projectOutlineEffortColumn      = new Column(columnName: 'ProjectOutlineEffort', type: Column::TYPE_FLOAT, description: 'The partner effort in the project outline phase');
        $fullProjectProposalCostsColumn  = new Column(columnName: 'FullProjectProposalCosts', type: Column::TYPE_FLOAT, description: 'The partner costs in the full project proposal phase');
        $fullProjectProposalEffortColumn = new Column(columnName: 'FullProjectProposalEffort', type: Column::TYPE_FLOAT, description: 'The partner effort in the full project proposal phase');
        $latestVersionCostsColumn        = new Column(columnName: 'LatestVersionCosts', type: Column::TYPE_FLOAT, description: 'The partner costs in the latest project version');
        $latestVersionEffortColumn       = new Column(columnName: 'LatestVersionEffort', type: Column::TYPE_FLOAT, description: 'The partner effort in the latest project version');

        $amount = $this->findCount(criteria: []);
        $i      = 0;

        while ($i < $amount) {
            $elements = $this->findSliced(offset: $i, criteria: []);

            /** @var Partner $partner */
            foreach ($elements as $partner) {
                $idColumn->addRow($partner->getId());
                $organisationIdColumn->addRow($partner->getOrganisation()->getId());
                $projectIdColumn->addRow($partner->getProject()->getId());
                $slugColumn->addRow($partner->getSlug());
                $organisationNameColumn->addRow($partner->getOrganisationName());
                $projectNameColumn->addRow($partner->getProjectName());
                $isActiveColumn->addRow($partner->isActive());
                $isCoordinatorColumn->addRow($partner->isCoordinator());
                $isSelfFundedColumn->addRow($partner->isSelfFunded());

                $technicalContactJson = json_encode($partner->getTechnicalContact());
                $technicalContactColumn->addRow($technicalContactJson === false ? null : $technicalContactJson);

                $projectOutlineCostsColumn->addRow($partner->getProjectOutlineCosts());
                $projectOutlineEffortColumn->addRow($partner->getProjectOutlineEffort());
                $fullProjectProposalCostsColumn->addRow($partner->getFullProjectProposalCosts());
                $fullProjectProposalEffortColumn->addRow($partner->getFullProjectProposalEffort());
                $latestVersionCostsColumn->addRow($partner->getLatestVersionCosts());
                $latestVersionEffortColumn->addRow($partner->getLatestVersionEffort());
            }

            //clear the entity manager to prevent piling up entities
            $this->entityManager->clear();

            $i += $this->chunkSize;
        }

        return [
            $idColumn,
            $organisationIdColumn,
            $projectIdColumn,
            $slugColumn,
            $organisationNameColumn,
            $projectNameColumn,
            $isActiveColumn,
            $isCoordinatorColumn,
            $isSelfFundedColumn,
            $technicalContactColumn,
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
            OrganisationColumns::class,
        ];
    }
}
