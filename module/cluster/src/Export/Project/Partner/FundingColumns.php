<?php

declare(strict_types=1);

namespace Cluster\Export\Project\Partner;

use Cluster\Entity\Project\Partner\Funding;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class FundingColumns extends AbstractEntityColumns
{
    protected string $name = 'cluster_project_partner_funding';

    protected string $entity = Funding::class;

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn          = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false);
        $yearColumn        = new Column(columnName: 'Year', type: Column::TYPE_INTEGER, isNullable: false);
        $dateUpdatedColumn = new Column(columnName: 'DateUpdated', type: Column::TYPE_DATE);
        $dateCreatedColumn = new Column(columnName: 'DateCreated', type: Column::TYPE_DATE);
        $statusIdColumn    = new Column(columnName: 'StatusId', type: Column::TYPE_INTEGER, isNullable: false);
        $partnerIdColumn   = new Column(columnName: 'PartnerId', type: Column::TYPE_INTEGER, isNullable: false);

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
