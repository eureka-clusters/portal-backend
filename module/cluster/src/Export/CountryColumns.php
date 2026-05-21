<?php

declare(strict_types=1);

namespace Cluster\Export;

use Cluster\Entity\Country;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class CountryColumns extends AbstractEntityColumns
{
    protected string $name = 'cluster_country';

    protected string  $entity      = Country::class;
    protected ?string $description = 'This export contains all countries. Use the Id column to resolve CountryId references from other exports, such as cluster_organisation and cluster_funder.';

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn      = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false, description: 'The unique identifier of the country');
        $cdColumn      = new Column(columnName: 'Cd', description: 'The two-letter country code');
        $countryColumn = new Column(columnName: 'Country', description: 'The name of the country');
        $iso3Column    = new Column(columnName: 'Iso3', description: 'The three-letter ISO country code');

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

}
