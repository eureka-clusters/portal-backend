<?php

declare(strict_types=1);

namespace Admin\Repository;

use Admin\Entity;
use Application\Repository\FilteredObjectRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Application\ValueObject\SearchFormResult;

use function sprintf;

final class Role extends EntityRepository implements FilteredObjectRepository
{
    public function findFiltered(SearchFormResult $searchFormResult): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(select: 'admin_entity_role');
        $qb->from(from: Entity\Role::class, alias: 'admin_entity_role');

        $qb = $this->applyRoleFilter(qb: $qb, searchFormResult: $searchFormResult);

        $direction = $searchFormResult->getDirection();

        switch ($searchFormResult->getOrder()) {
            case 'id':
                $qb->addOrderBy(sort: 'admin_entity_role.id', order: $direction);
                break;
            case 'name':
                $qb->addOrderBy(sort: 'admin_entity_role.description', order: $direction);
                break;
            default:
                $qb->addOrderBy(sort: 'admin_entity_role.description', order: Criteria::ASC);
        }

        return $qb;
    }

    public function applyRoleFilter(QueryBuilder $qb, SearchFormResult $searchFormResult): QueryBuilder
    {
        if ($searchFormResult->hasQuery()) {
            $qb->andWhere($qb->expr()->like(x: 'admin_entity_role.description', y: ':like'));
            $qb->setParameter(key: 'like', value: sprintf('%%%s%%', $searchFormResult->getQuery()));
        }

        if ($searchFormResult->hasFilterByKey(key: 'locked')) {
            $qb->andWhere($qb->expr()->in(x: 'admin_entity_role.id', y: Entity\Role::$lockedRoles));
        }

        return $qb;
    }
}
