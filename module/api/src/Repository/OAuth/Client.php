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

final class Client extends EntityRepository //implements ClientCredentialsInterface
{
    public function findFiltered(array $filter): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(select: 'api_entity_oauth_clients');
        $qb->from(from: Entity\OAuth\Client::class, alias: 'api_entity_oauth_clients');

        $qb = $this->applyRoleFilter(qb: $qb, filter: $filter);

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
                $qb->addOrderBy(sort: 'api_entity_oauth_clients.id', order: $direction);
                break;
            default:
                $qb->addOrderBy(sort: 'api_entity_oauth_clients.id', order: $direction);
        }

        return $qb;
    }

    public function applyRoleFilter(QueryBuilder $qb, array $filter): QueryBuilder
    {
        if (!empty($filter['query'])) {
            $qb->andWhere($qb->expr()->like(x: 'api_entity_oauth_clients.client', y: ':like'));
            $qb->setParameter(key: 'like', value: sprintf('%%%s%%', $filter['query']));
        }

        return $qb;
    }
}
