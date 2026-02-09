<?php

declare(strict_types=1);

namespace Cluster\Export;

use Cluster\Entity\Organisation;
use Cluster\Export\Organisation\TypeColumns as OrganisationTypeColumns;
use Jield\Export\Columns\AbstractEntityColumns;
use Jield\Export\ValueObject\Column;

final class OrganisationColumns extends AbstractEntityColumns
{
    protected string $name = 'cluster_organisation';

    protected string $entity = Organisation::class;

    /**
     * @return array<Column>
     */
    #[\Override]
    public function getColumns(): array
    {
        $idColumn        = new Column(columnName: 'Id', type: Column::TYPE_INTEGER, isNullable: false);
        $nameColumn      = new Column(columnName: 'Name', isNullable: false);
        $slugColumn      = new Column(columnName: 'Slug', isNullable: false);
        $countryIdColumn = new Column(columnName: 'CountryId', type: Column::TYPE_INTEGER, isNullable: false);
        $typeIdColumn    = new Column(columnName: 'TypeId', type: Column::TYPE_INTEGER, isNullable: false);

        $amount = $this->findCount(criteria: []);
        $i      = 0;

        while ($i < $amount) {
            $elements = $this->findSliced(offset: $i, criteria: []);

            /** @var Organisation $organisation */
            foreach ($elements as $organisation) {
                $idColumn->addRow($organisation->getId());
                $nameColumn->addRow($organisation->getName());
                $slugColumn->addRow($organisation->getSlug());
                $countryIdColumn->addRow($organisation->getCountry()->getId());
                $typeIdColumn->addRow($organisation->getType()->getId());
            }

            //clear the entity manager to prevent piling up entities
            $this->entityManager->clear();

            $i += $this->chunkSize;
        }

        return [
            $idColumn,
            $nameColumn,
            $slugColumn,
            $countryIdColumn,
            $typeIdColumn,
        ];
    }

    #[\Override]
    public function getDependencies(): array
    {
        return [
            CountryColumns::class,
            OrganisationTypeColumns::class,
        ];
    }
}
