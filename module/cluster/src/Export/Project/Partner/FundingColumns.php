<?php

declare(strict_types=1);

namespace Cluster\Export\Project\Partner;

use Cluster\Entity\Project\Partner\Funding;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class FundingColumns extends AbstractEntityColumns
{
    protected string $name = 'cluster_project_partner_funding';

    protected string  $entity      = Funding::class;
    protected ?string $description = 'This export contains the yearly funding records for project partners. StatusId links to cluster_version_status and PartnerId links to cluster_project_partner.';

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn          = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false, description: 'The unique identifier of the partner funding record');
        $yearColumn        = new Column(columnName: 'Year', type: Column::TYPE_INTEGER, isNullable: false, description: 'The funding year for this partner funding record');
        $dateUpdatedColumn = new Column(columnName: 'DateUpdated', type: Column::TYPE_DATE, description: 'The date when the partner funding record was last updated');
        $dateCreatedColumn = new Column(columnName: 'DateCreated', type: Column::TYPE_DATE, description: 'The date when the partner funding record was created');
        $statusIdColumn    = new Column(columnName: 'StatusId', type: Column::TYPE_INTEGER, isNullable: false, description: 'The id of the funding status for this record, which links to cluster_version_status');
        $partnerIdColumn   = new Column(columnName: 'PartnerId', type: Column::TYPE_INTEGER, isNullable: false, description: 'The id of the related project partner, which links to cluster_project_partner');

        $amount = $this->findCount(criteria: []);
        $i      = 0;

        while ($i < $amount) {
            $elements = $this->findSliced(offset: $i, criteria: []);

            /** @var Funding $funding */
            foreach ($elements as $funding) {
                $idColumn->addRow($funding->getId());
                $yearColumn->addRow($funding->getYear());
                $dateUpdatedColumn->addRow($funding->getDateUpdated());
                $dateCreatedColumn->addRow($funding->getDateCreated());
                $statusIdColumn->addRow($funding->getStatus()->getId());
                $partnerIdColumn->addRow($funding->getPartner()->getId());
            }

            //clear the entity manager to prevent piling up entities
            $this->entityManager->clear();

            $i += $this->chunkSize;
        }

        return [
            $idColumn,
            $yearColumn,
            $dateUpdatedColumn,
            $dateCreatedColumn,
            $statusIdColumn,
            $partnerIdColumn,
        ];
    }
}
