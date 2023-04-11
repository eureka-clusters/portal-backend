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
    public function findFiltered(SearchFormResult $searchFormResult): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(select: 'reporting_entity_storage_location');
        $qb->from(from: Entity\StorageLocation::class, alias: 'reporting_entity_storage_location');

        $direction = $searchFormResult->getDirection();

        switch ($searchFormResult->getOrder()) {
            case 'name':
                $qb->addOrderBy(sort: 'reporting_entity_storage_location.name', order: $direction);
                break;
            case 'excelFolder':
                $qb->addOrderBy(sort: 'reporting_entity_storage_location.excelFolder', order: $direction);
                break;
            case 'parquetFolder':
                $qb->addOrderBy(sort: 'reporting_entity_storage_location.parquetFolder', order: $direction);
                break;
            default:
                $qb->addOrderBy(sort: 'reporting_entity_storage_location.name', order: Criteria::ASC);
        }

        return $qb;
    }
}
