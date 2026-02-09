<?php

declare(strict_types=1);

namespace Cluster\Export\Funding;

use Cluster\Entity\Funding\Status;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class StatusColumns extends AbstractEntityColumns
{
    protected string $name = 'cluster_funding_status';

    protected string $entity = Status::class;

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn               = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false);
        $codeColumn             = new Column(columnName: 'Code', isNullable: false);
        $statusColumn           = new Column(columnName: 'Status', isNullable: false);
        $colorColumn            = new Column(columnName: 'Color', isNullable: false);
        $statusFundingColumn    = new Column(columnName: 'StatusFunding', isNullable: false);
        $isEvaluationColumn     = new Column(columnName: 'IsEvaluation', type: Column::TYPE_BOOLEAN, isNullable: false);
        $statusEvaluationColumn = new Column(columnName: 'StatusEvaluation');
        $sequenceColumn         = new Column(columnName: 'Sequence', type: Column::TYPE_INTEGER, isNullable: false);

        $amount = $this->findCount(criteria: []);
        $i      = 0;

        while ($i < $amount) {
            $elements = $this->findSliced(offset: $i, criteria: []);

            /** @var Status $status */
            foreach ($elements as $status) {
                $idColumn->addRow($status->getId());
                $codeColumn->addRow($status->getCode());
                $statusColumn->addRow($status->getStatus());
                $colorColumn->addRow($status->getColor());
                $statusFundingColumn->addRow($status->getStatusFunding());
                $isEvaluationColumn->addRow($status->isEvaluation());
                $statusEvaluationColumn->addRow($status->getStatusEvaluation());
                $sequenceColumn->addRow($status->getSequence());
            }

            //clear the entity manager to prevent piling up entities
            $this->entityManager->clear();

            $i += $this->chunkSize;
        }

        return [
            $idColumn,
            $codeColumn,
            $statusColumn,
            $colorColumn,
            $statusFundingColumn,
            $isEvaluationColumn,
            $statusEvaluationColumn,
            $sequenceColumn,
        ];
    }
}
