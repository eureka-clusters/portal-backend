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
        $qb->select('api_entity_oauth_clients');
        $qb->from(Entity\OAuth\Client::class, 'api_entity_oauth_clients');

        $qb = $this->applyRoleFilter($qb, $filter);

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
                $qb->addOrderBy('api_entity_oauth_clients.id', $direction);
                break;
            default:
                $qb->addOrderBy('api_entity_oauth_clients.id', $direction);
        }

        return $qb;
    }

    public function applyRoleFilter(QueryBuilder $qb, array $filter): QueryBuilder
    {
        if (!empty($filter['query'])) {
            $qb->andWhere($qb->expr()->like('api_entity_oauth_clients.client', ':like'));
            $qb->setParameter('like', sprintf('%%%s%%', $filter['query']));
        }

        return $qb;
    }
}
