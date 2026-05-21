<?php

declare(strict_types=1);

namespace Admin\Export\User;

use Admin\Entity\User;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class RoleColumns extends AbstractEntityColumns
{
    protected string $name = 'admin_user_role';

    protected string  $entity      = User::class;
    protected ?string $description = 'This export contains the user-role assignments. UserId links to the user export and RoleId links to the admin_role export.';

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $userIdColumn = new Column(columnName: 'UserId', type: Column::TYPE_INTEGER, isNullable: false, description: 'The id of the user linked to the admin_user');
        $roleIdColumn = new Column(columnName: 'RoleId', type: Column::TYPE_INTEGER, isNullable: false, description: 'The id of the role linked to the admin_role');

        $amount = $this->findCount(criteria: []);
        $i      = 0;

        while ($i < $amount) {
            $users = $this->findSliced(offset: $i, criteria: []);

            /** @var User $user */
            foreach ($users as $user) {
                foreach ($user->getRoles() as $role) {
                    $userIdColumn->addRow($user->getId());
                    $roleIdColumn->addRow($role->getId());
                }
            }

            //clear the entity manager to prevent piling up entities
            $this->entityManager->clear();

            $i += $this->chunkSize;
        }

        return [
            $userIdColumn,
            $roleIdColumn,
        ];
    }
}
