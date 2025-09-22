<?php

declare(strict_types=1);

namespace Reporting\Repository;

use Application\Repository\FilteredObjectRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Jield\Search\ValueObject\SearchFormResult;
use Override;
use Reporting\Entity;

final class StorageLocationRepository extends EntityRepository implements FilteredObjectRepository
{
    #[Override]
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
                    $qb->expr()->like(x: 'reporting_entity_storage_location.folder', y: ':like'),
                    $qb->expr()->like(x: 'reporting_entity_storage_location.exportFileType', y: ':like'),
                )
            );
            $qb->setParameter(key: 'like', value: sprintf('%%%s%%', $searchFormResult->getQuery()));
        }

        match ($searchFormResult->getOrder()) {
            'id' => $qb->addOrderBy(sort: 'reporting_entity_storage_location.id', order: $direction),
            'name' => $qb->addOrderBy(sort: 'reporting_entity_storage_location.name', order: $direction),
            'container' => $qb->addOrderBy(sort: 'reporting_entity_storage_location.container', order: $direction),
            'folder' => $qb->addOrderBy(sort: 'reporting_entity_storage_location.folder', order: $direction),
            'file-type' => $qb->addOrderBy(sort: 'reporting_entity_storage_location.exportFileType', order: $direction),
            default => $qb->addOrderBy(sort: 'reporting_entity_storage_location.name', order: Order::Ascending->value),
        };

        return $qb;
    }
}
