<?php

declare(strict_types=1);

namespace Admin\Repository;

use Admin\Entity;
use Application\Repository\FilteredObjectRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Search\ValueObject\SearchFormResult;

use function sprintf;

final class Role extends EntityRepository implements FilteredObjectRepository
{
    public function findFiltered(SearchFormResult $searchFormResult): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('admin_entity_role');
        $qb->from(Entity\Role::class, 'admin_entity_role');

        $qb = $this->applyRoleFilter($qb, $searchFormResult);

        $direction = $searchFormResult->getDirection();

        switch ($searchFormResult->getOrder()) {
            case 'id':
                $qb->addOrderBy('admin_entity_role.id', $direction);
                break;
            case 'name':
                $qb->addOrderBy('admin_entity_role.description', $direction);
                break;
            default:
                $qb->addOrderBy('admin_entity_role.description', Criteria::ASC);
        }

        return $qb;
    }

    public function applyRoleFilter(QueryBuilder $qb, SearchFormResult $searchFormResult): QueryBuilder
    {
        if ($searchFormResult->hasQuery()) {
            $qb->andWhere($qb->expr()->like('admin_entity_role.description', ':like'));
            $qb->setParameter('like', sprintf('%%%s%%', $searchFormResult->getQuery()));
        }

        if ($searchFormResult->hasFilterByKey('locked')) {
            $qb->andWhere($qb->expr()->in('admin_entity_role.id', Entity\Role::$lockedRoles));
        }

        return $qb;
    }
}
