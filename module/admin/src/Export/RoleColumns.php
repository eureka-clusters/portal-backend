<?php

declare(strict_types=1);

namespace Admin\Export;

use Admin\Entity\Role;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class RoleColumns extends AbstractEntityColumns
{
    protected string $name = 'admin_role';

    protected string  $entity      = Role::class;
    protected ?string $description = 'This export contains all admin roles. Use the Id column to resolve RoleId references from the user_role export.';

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false, description: 'The unique identifier of the role');
        $descriptionColumn = new Column(columnName: 'Description', isNullable: false, description: 'The human-readable description of the role');

        $amount = $this->findCount(criteria: []);
        $i      = 0;

        while ($i < $amount) {
            $elements = $this->findSliced(offset: $i, criteria: []);

            /** @var Role $role */
            foreach ($elements as $role) {
                $idColumn->addRow($role->getId());
                $descriptionColumn->addRow($role->getDescription());
            }

            //clear the entity manager to prevent piling up entities
            $this->entityManager->clear();

            $i += $this->chunkSize;
        }

        return [
            $idColumn,
            $descriptionColumn,
        ];
    }
}
