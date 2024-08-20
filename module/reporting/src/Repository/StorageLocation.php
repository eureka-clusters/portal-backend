<?php

declare(strict_types=1);

namespace Reporting\Repository;

use Application\Repository\FilteredObjectRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Jield\Search\ValueObject\SearchFormResult;
use Reporting\Entity;

final class StorageLocation extends EntityRepository implements FilteredObjectRepository
{
    #[\Override]
    public function findFiltered(SearchFormResult $searchFormResult): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(select: 'reporting_entity_storage_location');
        $qb->from(from: Entity\StorageLocation::class, alias: 'reporting_entity_storage_location');

        $direction = $searchFormResult->getDirection();

        if ($searchFormResult->hasQuery()) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like(x: 'reporting_entity_storage_location.name', y: ':like'),
                    $qb->expr()->like(x: 'reporting_entity_storage_location.container', y: ':like'),
                )
            );
            $qb->setParameter(key: 'like', value: sprintf('%%%s%%', $searchFormResult->getQuery()));
        }

        match ($searchFormResult->getOrder()) {
            'id' => $qb->addOrderBy(sort: 'reporting_entity_storage_location.id', order: $direction),
            'name' => $qb->addOrderBy(sort: 'reporting_entity_storage_location.name', order: $direction),
            'container' => $qb->addOrderBy(sort: 'reporting_entity_storage_location.container', order: $direction),
            'excelFolder' => $qb->addOrderBy(sort: 'reporting_entity_storage_location.excelFolder', order: $direction),
            'parquetFolder' => $qb->addOrderBy(
                sort: 'reporting_entity_storage_location.parquetFolder',
                order: $direction
            ),
            default => $qb->addOrderBy(
                sort: 'reporting_entity_storage_location.name',
                order: \Doctrine\Common\Collections\Order::Ascending->value
            ),
        };

        return $qb;
    }
}
