<?php

declare(strict_types=1);

namespace Cluster\Repository\Cluster;

use Cluster\Entity\Cluster\Group;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Jield\Search\ValueObject\SearchFormResult;

class GroupRepository extends EntityRepository
{
    public function findFiltered(SearchFormResult $searchFormResult): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(select: 'cluster_entity_cluster_group');
        $qb->from(from: Group::class, alias: 'cluster_entity_cluster_group');

        $qb = $this->applyProjectFilter(qb: $qb, searchFormResult: $searchFormResult);

        $direction = $searchFormResult->getDirection();

        switch ($searchFormResult->getOrder()) {
            case 'id':
                $qb->addOrderBy(sort: 'cluster_entity_cluster_group.id', order: $direction);
                break;
            case 'name':
                $qb->addOrderBy(sort: 'cluster_entity_cluster_group.name', order: $direction);
                break;
            default:
                $qb->addOrderBy(sort: 'cluster_entity_cluster_group.name', order: Criteria::ASC);
        }

        return $qb;
    }

    public function applyProjectFilter(QueryBuilder $qb, SearchFormResult $searchFormResult): QueryBuilder
    {
        if ($searchFormResult->hasQuery()) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like(x: 'cluster_entity_cluster_group.name', y: ':like'),
                    $qb->expr()->like(x: 'cluster_entity_cluster_group.description', y: ':like')
                )
            );
            $qb->setParameter(key: 'like', value: sprintf('%%%s%%', $searchFormResult->getQuery()));
        }

        return $qb;
    }
}
