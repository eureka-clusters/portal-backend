<?php

declare(strict_types=1);

namespace Api\Repository\OAuth;

use Api\Entity;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

use function in_array;
use function sprintf;
use function strtoupper;

final class Scope extends EntityRepository
{
    public function findFiltered(array $filter): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('api_entity_oauth_scope');
        $qb->from(Entity\OAuth\Scope::class, 'api_entity_oauth_scope');

        $qb = $this->applyScopeFilter($qb, $filter);

        $direction = Criteria::ASC;
        if (
            isset($filter['direction']) && in_array(
                strtoupper($filter['direction']),
                [
                    Criteria::ASC,
                    Criteria::DESC,
                ],
                true
            )
        ) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'id':
                $qb->addOrderBy('api_entity_oauth_scope.id', $direction);
                break;
            case 'scope':
                $qb->addOrderBy('api_entity_oauth_scope.scope', $direction);
                break;
            default:
                $qb->addOrderBy('api_entity_oauth_scope.id', $direction);
        }

        return $qb;
    }

    public function applyScopeFilter(QueryBuilder $qb, array $filter): QueryBuilder
    {
        if (! empty($filter['query'])) {
            $qb->andWhere($qb->expr()->like('api_entity_oauth_scope.scope', ':like'));
            $qb->setParameter('like', sprintf('%%%s%%', $filter['query']));
        }

        return $qb;
    }
}
