<?php

declare(strict_types=1);

namespace Cluster\Export;

use Cluster\Entity\Funder;
use Cluster\Export\CountryColumns;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class

FunderColumns extends AbstractEntityColumns
{
    protected string $name = 'funder';

    protected string $entity = Funder::class;

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn        = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false);
        $userIdColumn    = new Column(columnName: 'UserId', type: Column::TYPE_INTEGER, isNullable: false);
        $countryIdColumn = new Column(columnName: 'CountryId', type: Column::TYPE_INTEGER, isNullable: false);

        $amount = $this->findCount(criteria: []);
        $i      = 0;

        while ($i < $amount) {
            $elements = $this->findSliced(offset: $i);

            /** @var Funder $funder */
            foreach ($elements as $funder) {
                $idColumn->addRow($funder->getId());
                $userIdColumn->addRow($funder->getUser()->getId());
                $countryIdColumn->addRow($funder->getCountry()->getId());
            }

            //clear the entity manager to prevent piling up entities
            $this->entityManager->clear();

            $i += $this->chunkSize;
        }

        return [
            $idColumn,
            $userIdColumn,
            $countryIdColumn,
        ];
    }

    #[\Override]
    public function getDependencies(): array
    {
        return [
            CountryColumns::class,
        ];
    }
}
