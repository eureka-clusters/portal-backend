<?php

declare(strict_types=1);

namespace Admin\Repository;

use Admin\Entity\Selection\Sql;
use Admin\Entity\Selection;
use Admin\Entity;
use Application\Repository\FilteredObjectRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Search\ValueObject\SearchFormResult;

use function count;
use function sprintf;

final class User extends EntityRepository implements FilteredObjectRepository
{
    public function findFiltered(SearchFormResult $searchFormResult): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('admin_entity_user');
        $qb->from(Entity\User::class, 'admin_entity_user');

        $qb = $this->applyUserFilter($qb, $searchFormResult);

        $direction = $searchFormResult->getDirection();

        switch ($searchFormResult->getOrder()) {
            case 'name':
                $qb->addOrderBy('admin_entity_user.username', $direction);
                break;
            case 'firstname':
                $qb->addOrderBy('admin_entity_user.firstName', $direction);
                break;
            case 'lastname':
                $qb->addOrderBy('admin_entity_user.lastName', $direction);
                break;
            case 'email':
                $qb->addOrderBy('admin_entity_user.email', $direction);
                break;
            case 'add-date':
                $qb->addOrderBy('admin_entity_user.dateCreated', $direction);
                break;
            case 'last-update':
                $qb->addOrderBy('admin_entity_user.lastUpdate', $direction);
                break;
            default:
                $qb->addOrderBy('admin_entity_user.lastName', Criteria::ASC);
        }

        return $qb;
    }

    public function applyUserFilter(QueryBuilder $qb, SearchFormResult $searchFormResult): QueryBuilder
    {
        if ($searchFormResult->hasQuery()) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('admin_entity_user.username', ':like'),
                    $qb->expr()->like('admin_entity_user.firstName', ':like'),
                    $qb->expr()->like('admin_entity_user.lastName', ':like'),
                    $qb->expr()->like('admin_entity_user.email', ':like')
                )
            );
            $qb->setParameter('like', sprintf('%%%s%%', $searchFormResult->getQuery()));
        }

        //Go over the filterArray and apply the filters which are there
        if ($searchFormResult->hasFilterByKey('roles')) {
            $qb->join('admin_entity_user.roles', 'roles');
            $qb->andWhere($qb->expr()->in('roles.id', $searchFormResult->getFilterByKey('roles')));
        }

        return $qb;
    }

    public function findActiveUsers(string $sort): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('admin_entity_user');
        $qb->from(Entity\User::class, 'admin_entity_user');

        if ($sort === 'lastname') {
            $qb->addOrderBy('admin_entity_user.lastName', Criteria::ASC);
        } else {
            $qb->addOrderBy('admin_entity_user.firstName', Criteria::ASC);
        }

        $qb->where($qb->expr()->isNull('admin_entity_user.dateEnd'));
        return $qb->getQuery()->getResult();
    }

    public function searchUserByName(string $name): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('admin_entity_user');
        $qb->from(Entity\User::class, 'admin_entity_user');

        $qb->andWhere($qb->expr()->like('admin_entity_user.displayName', ':like'));
        $qb->setParameter('like', sprintf('%%%s%%', $name));

        return $qb->getQuery()->getArrayResult();
    }

    public function isUserInSelectionSQL(Entity\User $user, Sql $sql): bool
    {
        $resultSetMap = new ResultSetMapping();
        $resultSetMap->addEntityResult(Entity\User::class, 'admin_entity_user');
        $resultSetMap->addFieldResult('admin_entity_user', 'user_id', 'id');
        $query = $this->getEntityManager()->createNativeQuery(
            'SELECT id AS user_id FROM admin_user WHERE id IN ('
            . $sql->getQuery() . ') AND id = ' . $user->getId(),
            $resultSetMap
        );

        return (is_countable($query->getResult()) ? count($query->getResult()) : 0) > 0;
    }

    public function findUsersBySelectionSQL(Sql $sql, bool $toArray = false): array
    {
        $resultSetMap = new ResultSetMapping();
        $resultSetMap->addEntityResult(Entity\User::class, 'user');

        $resultSetMap->addFieldResult('user', 'id', 'id');
        $resultSetMap->addFieldResult('user', 'email', 'email');
        $resultSetMap->addFieldResult('user', 'firstname', 'firstName');
        $resultSetMap->addFieldResult('user', 'lastname', 'lastName');

        $query = $this->getEntityManager()->createNativeQuery(
            'SELECT 
                      admin_user.id, 
                      admin_user.email, 
                      admin_user.firstname, 
                      admin_user.lastname 
                FROM admin_user
            WHERE admin_user.id IN (' . $sql->getQuery()
            . ') AND dateEnd IS NULL ORDER BY lastName',
            $resultSetMap
        );

        if ($toArray) {
            return $query->getResult(AbstractQuery::HYDRATE_ARRAY);
        }

        return $query->getResult();
    }

    public function findUsersBySelectionUser(Selection $selection, bool $toArray = false): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('admin_entity_user');
        $qb->from(Entity\User::class, 'admin_entity_user');
        $qb->join('admin_entity_user.selectionUser', 'sc');
        $qb->distinct('admin_entity_user.id');
        $qb->andWhere($qb->expr()->isNull('admin_entity_user.dateEnd'));
        $qb->andWhere('sc.selection = ?1');
        $qb->setParameter(1, $selection->getId());
        $qb->orderBy('admin_entity_user.lastName');

        if ($toArray) {
            return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
        }

        return $qb->getQuery()->getResult();
    }

    public function findAmountOfUsersInSelection(Selection $selection): int
    {
        if (null !== $selection->getSql()) {
            $resultSetMap = new ResultSetMapping();
            $resultSetMap->addEntityResult(Entity\User::class, 'user');
            $resultSetMap->addFieldResult('user', 'foo', 'foo');

            $query = sprintf(
                'SELECT COUNT(admin_user.id) FROM admin_user
            WHERE id IN (%s) AND dateEnd IS NULL',
                $selection->getSql()->getQuery()
            );

            $statement = $this->_em->getConnection()->prepare($query);

            return (int)$statement->executeQuery()->fetchOne();
        }

        $qb = $this->_em->createQueryBuilder();
        $qb->select('COUNT(admin_entity_user)');
        $qb->from(Entity\User::class, 'admin_entity_user');
        $qb->join('admin_entity_user.selectionUser', 'admin_entity_selection_user');
        $qb->distinct('admin_entity_user.id');
        $qb->andWhere($qb->expr()->isNull('admin_entity_user.dateEnd'));
        $qb->andWhere('admin_entity_selection_user.selection = ?1');
        $qb->setParameter(1, $selection->getId());

        return (int)$qb->getQuery()->getSingleScalarResult();
    }
}
