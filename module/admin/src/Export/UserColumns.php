<?php

declare(strict_types=1);

namespace Admin\Export;

use Admin\Entity\User;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class UserColumns extends AbstractEntityColumns
{
    protected string $name = 'admin_user';

    protected string  $entity      = User::class;
    protected ?string $description = 'This export contains all admin users. The Id column is referenced by UserId in the user_role export.';

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn                             = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false, description: 'The unique identifier of the user');
        $firstNameColumn                      = new Column(columnName: 'FirstName', isNullable: false, description: 'The first name of the user');
        $lastNameColumn                       = new Column(columnName: 'LastName', isNullable: false, description: 'The last name of the user');
        $emailAddressColumn                   = new Column(columnName: 'EmailAddress', isNullable: false, description: 'The email address of the user');
        $dateCreatedColumn                    = new Column(columnName: 'DateCreated', type: Column::TYPE_DATE, isNullable: false, description: 'The date when the user record was created');
        $dateUpdatedColumn                    = new Column(columnName: 'DateUpdated', type: Column::TYPE_DATE, description: 'The date when the user record was last updated');
        $dateEndColumn                        = new Column(columnName: 'DateEnd', type: Column::TYPE_DATE, description: 'The date when the user account ended');
        $isEurekaSecretariatStaffMemberColumn = new Column(
            columnName: 'IsEurekaSecretariatStaffMember',
            type: Column::TYPE_BOOLEAN,
            isNullable: false,
            description: 'Whether the user is marked as a Eureka secretariat staff member'
        );

        $amount = $this->findCount(criteria: []);
        $i      = 0;

        while ($i < $amount) {
            $users = $this->findSliced(offset: $i, criteria: []);

            /** @var User $user */
            foreach ($users as $user) {
                $idColumn->addRow($user->getId());
                $firstNameColumn->addRow($user->getFirstName());
                $lastNameColumn->addRow($user->getLastName());
                $emailAddressColumn->addRow($user->getEmail());
                $dateCreatedColumn->addRow($user->getDateCreated());
                $dateUpdatedColumn->addRow($user->getDateUpdated());
                $dateEndColumn->addRow($user->getDateEnd());
                $isEurekaSecretariatStaffMemberColumn->addRow($user->isEurekaSecretariatStaffMember());
            }

            //clear the entity manager to prevent piling up entities
            $this->entityManager->clear();

            $i += $this->chunkSize;
        }

        return [
            $idColumn,
            $firstNameColumn,
            $lastNameColumn,
            $emailAddressColumn,
            $dateCreatedColumn,
            $dateUpdatedColumn,
            $dateEndColumn,
            $isEurekaSecretariatStaffMemberColumn,
        ];
    }

    #[\Override]
    public function getDependencies(): array
    {
        return [
            'role'      => RoleColumns::class,
            'user_role' => \Admin\Export\User\RoleColumns::class,
        ];
    }
}
