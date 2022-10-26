<?php

declare(strict_types=1);

namespace Admin\Repository;

use Admin\Entity;
use Application\Repository\FilteredObjectRepository;
use Application\ValueObject\SearchFormResult;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

use function sprintf;

final class User extends EntityRepository implements FilteredObjectRepository
{
    public function findFiltered(SearchFormResult $searchFormResult): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(select: 'admin_entity_user');
        $qb->from(from: Entity\User::class, alias: 'admin_entity_user');

        $qb = $this->applyUserFilter(qb: $qb, searchFormResult: $searchFormResult);

        $direction = $searchFormResult->getDirection();

        switch ($searchFormResult->getOrder()) {
            case 'firstname':
                $qb->addOrderBy(sort: 'admin_entity_user.firstName', order: $direction);
                break;
            case 'lastname':
                $qb->addOrderBy(sort: 'admin_entity_user.lastName', order: $direction);
                break;
            case 'email':
                $qb->addOrderBy(sort: 'admin_entity_user.email', order: $direction);
                break;
            case 'add-date':
                $qb->addOrderBy(sort: 'admin_entity_user.dateCreated', order: $direction);
                break;
            case 'last-update':
                $qb->addOrderBy(sort: 'admin_entity_user.lastUpdate', order: $direction);
                break;
            default:
                $qb->addOrderBy(sort: 'admin_entity_user.lastName', order: Criteria::ASC);
        }

        return $qb;
    }

    public function applyUserFilter(QueryBuilder $qb, SearchFormResult $searchFormResult): QueryBuilder
    {
        if ($searchFormResult->hasQuery()) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like(x: 'admin_entity_user.firstName', y: ':like'),
                    $qb->expr()->like(x: 'admin_entity_user.lastName', y: ':like'),
                    $qb->expr()->like(x: 'admin_entity_user.email', y: ':like')
                )
            );
            $qb->setParameter(key: 'like', value: sprintf('%%%s%%', $searchFormResult->getQuery()));
        }

        //Go over the filterArray and apply the filters which are there
        if ($searchFormResult->hasFilterByKey('roles')) {
            $qb->join(join: 'admin_entity_user.roles', alias: 'roles');
            $qb->andWhere($qb->expr()->in(x: 'roles.id', y: $searchFormResult->getFilterByKey('roles')));
        }

        return $qb;
    }
}
