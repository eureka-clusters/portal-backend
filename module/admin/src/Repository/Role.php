<?php

declare(strict_types=1);

namespace Admin\Repository;

use Admin\Entity;
use Application\Repository\FilteredObjectRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Jield\Search\ValueObject\SearchFormResult;
use function sprintf;

final class Role extends EntityRepository implements FilteredObjectRepository
{
    #[\Override]
    public function findFiltered(SearchFormResult $searchFormResult): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(select: 'admin_entity_role');
        $qb->from(from: Entity\Role::class, alias: 'admin_entity_role');

        $qb = $this->applyRoleFilter(qb: $qb, searchFormResult: $searchFormResult);

        $direction = $searchFormResult->getDirection();

        match ($searchFormResult->getOrder()) {
            'id' => $qb->addOrderBy(sort: 'admin_entity_role.id', order: $direction),
            'description' => $qb->addOrderBy(sort: 'admin_entity_role.description', order: $direction),
            default => $qb->addOrderBy(sort: 'admin_entity_role.description', order: \Doctrine\Common\Collections\Order::Ascending->value),
        };

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
