<?php

declare(strict_types=1);

namespace Cluster\Export;

use Cluster\Entity\Country;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class CountryColumns extends AbstractEntityColumns
{
    protected string $name = 'country';

    protected string $entity = Country::class;

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn      = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false);
        $cdColumn      = new Column(columnName: 'Cd');
        $countryColumn = new Column(columnName: 'Country');
        $iso3Column    = new Column(columnName: 'Iso3');

        $amount = $this->findCount(criteria: []);
        $i      = 0;

        while ($i < $amount) {
            $elements = $this->findSliced(offset: $i, criteria: []);

            /** @var Country $country */
            foreach ($elements as $country) {
                $idColumn->addRow($country->getId());
                $cdColumn->addRow($country->getCd());
                $countryColumn->addRow($country->getCountry());
                $iso3Column->addRow($country->getIso3());
            }

            //clear the entity manager to prevent piling up entities
            $this->entityManager->clear();

            $i += $this->chunkSize;
        }

        return [
            $idColumn,
            $cdColumn,
            $countryColumn,
            $iso3Column,
        ];
    }

    #[\Override]
    public function getDependencies(): array
    {
        return [
            FunderColumns::class
        ];
    }

}
