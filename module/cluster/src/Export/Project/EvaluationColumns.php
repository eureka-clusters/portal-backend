<?php

declare(strict_types=1);

namespace Cluster\Export\Project;

use Cluster\Entity\Project\Evaluation;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class EvaluationColumns extends AbstractEntityColumns
{
    protected string $name = 'cluster_project_evaluation';

    protected string $entity = Evaluation::class;

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn              = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false);
        $descriptionColumn     = new Column(columnName: 'Description', isNullable: false);
        $dateCreatedColumn     = new Column(columnName: 'DateCreated', isNullable: false);
        $statusIdColumn        = new Column(columnName: 'StatusId', type: Column::TYPE_INTEGER, isNullable: false);
        $userIdColumn          = new Column(columnName: 'UserId', type: Column::TYPE_INTEGER, isNullable: false);
        $countryIdColumn       = new Column(columnName: 'CountryId', type: Column::TYPE_INTEGER, isNullable: false);
        $projectIdColumn       = new Column(columnName: 'ProjectId', type: Column::TYPE_INTEGER, isNullable: false);
        $projectVersionIdColumn = new Column(columnName: 'ProjectVersionId', type: Column::TYPE_INTEGER);

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
}
