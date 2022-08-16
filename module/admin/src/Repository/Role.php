<?php

declare(strict_types=1);

namespace Admin\Repository;

use Admin\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

use function in_array;
use function sprintf;
use function strtoupper;

final class Role extends EntityRepository
{
    public function findFiltered(array $filter): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('admin_entity_role');
        $qb->from(Entity\Role::class, 'admin_entity_role');

        if (null !== $filter) {
            $qb = $this->applyRoleFilter($qb, $filter);
        }

        $direction = 'ASC';
        if (isset($filter['direction']) && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'], true)) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'id':
                $qb->addOrderBy('admin_entity_role.id', $direction);
                break;
            case 'name':
                $qb->addOrderBy('admin_entity_role.description', $direction);
                break;
            default:
                $qb->addOrderBy('admin_entity_role.description', $direction);
        }

        return $qb;
    }

    public function applyRoleFilter(QueryBuilder $qb, array $filter): QueryBuilder
    {
        if (!empty($filter['query'])) {
            $qb->andWhere($qb->expr()->like('admin_entity_role.description', ':like'));
            $qb->setParameter('like', sprintf('%%%s%%', $filter['query']));
        }

        if (isset($filter['filter']['locked'])) {
            $qb->andWhere($qb->expr()->in('admin_entity_role.id', Entity\Role::$lockedRoles));
        }

        return $qb;
    }
}
