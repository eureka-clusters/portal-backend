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

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn                       = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false);
        $organisationIdColumn           = new Column(columnName: 'OrganisationId', type: Column::TYPE_INTEGER, isNullable: false);
        $projectIdColumn                = new Column(columnName: 'ProjectId', type: Column::TYPE_INTEGER, isNullable: false);
        $slugColumn                     = new Column(columnName: 'Slug', isNullable: false);
        $organisationNameColumn         = new Column(columnName: 'OrganisationName', isNullable: false);
        $projectNameColumn              = new Column(columnName: 'ProjectName', isNullable: false);
        $isActiveColumn                 = new Column(columnName: 'IsActive', type: Column::TYPE_BOOLEAN, isNullable: false);
        $isCoordinatorColumn            = new Column(columnName: 'IsCoordinator', type: Column::TYPE_BOOLEAN, isNullable: false);
        $isSelfFundedColumn             = new Column(columnName: 'IsSelfFunded', type: Column::TYPE_BOOLEAN, isNullable: false);
        $technicalContactColumn         = new Column(columnName: 'TechnicalContact');
        $projectOutlineCostsColumn      = new Column(columnName: 'ProjectOutlineCosts', type: Column::TYPE_FLOAT);
        $projectOutlineEffortColumn     = new Column(columnName: 'ProjectOutlineEffort', type: Column::TYPE_FLOAT);
        $fullProjectProposalCostsColumn = new Column(columnName: 'FullProjectProposalCosts', type: Column::TYPE_FLOAT);
        $fullProjectProposalEffortColumn = new Column(columnName: 'FullProjectProposalEffort', type: Column::TYPE_FLOAT);
        $latestVersionCostsColumn       = new Column(columnName: 'LatestVersionCosts', type: Column::TYPE_FLOAT);
        $latestVersionEffortColumn      = new Column(columnName: 'LatestVersionEffort', type: Column::TYPE_FLOAT);

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
