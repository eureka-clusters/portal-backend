<?php

declare(strict_types=1);

namespace Cluster\Export\Version;

use Cluster\Entity\Version\Type;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class TypeColumns extends AbstractEntityColumns
{
    protected string $name = 'cluster_version_type';

    protected string $entity = Type::class;

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn          = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false);
        $typeColumn        = new Column(columnName: 'Type', isNullable: false);
        $descriptionColumn = new Column(columnName: 'Description', isNullable: false);

        $amount = $this->findCount(criteria: []);
        $i      = 0;

        while ($i < $amount) {
            $elements = $this->findSliced(offset: $i, criteria: []);

            /** @var Type $type */
            foreach ($elements as $type) {
                $idColumn->addRow($type->getId());
                $typeColumn->addRow($type->getType());
                $descriptionColumn->addRow($type->getDescription());
            }

            //clear the entity manager to prevent piling up entities
            $this->entityManager->clear();

            $i += $this->chunkSize;
        }

        return [
            $idColumn,
            $typeColumn,
            $descriptionColumn,
        ];
    }
}
