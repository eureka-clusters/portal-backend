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
        $qb->select(select: 'api_entity_oauth_scope');
        $qb->from(from: Entity\OAuth\Scope::class, alias: 'api_entity_oauth_scope');

        $qb = $this->applyScopeFilter(qb: $qb, filter: $filter);

        $direction = Criteria::ASC;
        if (
            isset($filter['direction']) && in_array(
                needle: strtoupper(string: $filter['direction']),
                haystack: [
                    Criteria::ASC,
                    Criteria::DESC,
                ],
                strict: true
            )
        ) {
            $direction = strtoupper(string: $filter['direction']);
        }

        switch ($filter['order']) {
            case 'id':
                $qb->addOrderBy(sort: 'api_entity_oauth_scope.id', order: $direction);
                break;
            case 'scope':
                $qb->addOrderBy(sort: 'api_entity_oauth_scope.scope', order: $direction);
                break;
            default:
                $qb->addOrderBy(sort: 'api_entity_oauth_scope.id', order: $direction);
        }

        return $qb;
    }

    public function applyScopeFilter(QueryBuilder $qb, array $filter): QueryBuilder
    {
        if (!empty($filter['query'])) {
            $qb->andWhere($qb->expr()->like(x: 'api_entity_oauth_scope.scope', y: ':like'));
            $qb->setParameter(key: 'like', value: sprintf('%%%s%%', $filter['query']));
        }

        return $qb;
    }
}
