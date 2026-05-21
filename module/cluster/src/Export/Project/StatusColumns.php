<?php

declare(strict_types=1);

namespace Cluster\Export\Project;

use Cluster\Entity\Project\Status;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class StatusColumns extends AbstractEntityColumns
{
    protected string $name = 'cluster_project_status';

    protected string $entity = Status::class;
    protected ?string $description = 'This export contains the available project statuses. Use the Id column to resolve StatusId references from cluster_project.';

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn     = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false, description: 'The unique identifier of the project status');
        $statusColumn = new Column(columnName: 'Status', isNullable: false, description: 'The label of the project status');

        $amount = $this->findCount(criteria: []);
        $i      = 0;

        while ($i < $amount) {
            $elements = $this->findSliced(offset: $i, criteria: []);

            /** @var Status $status */
            foreach ($elements as $status) {
                $idColumn->addRow($status->getId());
                $statusColumn->addRow($status->getStatus());
            }

            //clear the entity manager to prevent piling up entities
            $this->entityManager->clear();

            $i += $this->chunkSize;
        }

        return [
            $idColumn,
            $statusColumn,
        ];
    }
}
