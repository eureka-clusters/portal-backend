<?php

declare(strict_types=1);

namespace Cluster\Export\Project;

use Cluster\Entity\Project\Area;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class AreaColumns extends AbstractEntityColumns
{
    protected string $name = 'cluster_project_area';

    protected string $entity = Area::class;

    /**
     * @return array<Column>
     * @throws \JsonException
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn        = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false);
        $projectIdColumn = new Column(columnName: 'ProjectId', type: Column::TYPE_INTEGER, isNullable: false);
        $codeColumn      = new Column(columnName: 'Code', type: Column::TYPE_STRING, isNullable: false);
        $labelColumn     = new Column(columnName: 'Label', type: Column::TYPE_STRING, isNullable: false);
        $typeColumn      = new Column(columnName: 'Type', type: Column::TYPE_STRING, isNullable: false);


        $amount = $this->findCount(criteria: []);
        $i      = 0;

        while ($i < $amount) {
            $elements = $this->findSliced(offset: $i, criteria: []);

            /** @var Area $area */
            foreach ($elements as $area) {
                $idColumn->addRow($area->getId());
                $projectIdColumn->addRow($area->getProject()->getId());
                $codeColumn->addRow($area->getCode());
                $labelColumn->addRow($area->getLabel());
                $typeColumn->addRow($area->getType());
            }

            //clear the entity manager to prevent piling up entities
            $this->entityManager->clear();

            $i += $this->chunkSize;
        }

        return [
            $idColumn,
            $projectIdColumn,
            $codeColumn,
            $labelColumn,
            $typeColumn,
        ];
    }
}
