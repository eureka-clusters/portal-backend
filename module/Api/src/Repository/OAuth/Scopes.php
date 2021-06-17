<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Api\Repository\OAuth;

use Api\Entity;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

use function in_array;
use function sprintf;

/**
 * Class Scopes
 * @package Api\Repository\OAuth
 */
final class Scopes extends EntityRepository
{
    public function findFiltered(array $filter): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('api_entity_oauth_scopes');
        $qb->from(Entity\OAuth\Scopes::class, 'api_entity_oauth_scopes');

        if (null !== $filter) {
            $qb = $this->applyScopeFilter($qb, $filter);
        }

        $direction = Criteria::ASC;
        if (
            isset($filter['direction']) && in_array(
                strtoupper($filter['direction']),
                [
                    Criteria::ASC,
                    Criteria::DESC
                ],
                true
            )
        ) {
            $direction = strtoupper($filter['direction']);
        }


        switch ($filter['order']) {
            case 'id':
                $qb->addOrderBy('api_entity_oauth_scopes.id', $direction);
                break;
            case 'scope':
                $qb->addOrderBy('api_entity_oauth_scopes.scope', $direction);
                break;
            default:
                $qb->addOrderBy('api_entity_oauth_scopes.id', $direction);
        }

        return $qb;
    }

    public function applyScopeFilter(QueryBuilder $qb, array $filter): QueryBuilder
    {
        if (! empty($filter['query'])) {
            $qb->andWhere($qb->expr()->like('api_entity_oauth_scopes.scope', ':like'));
            $qb->setParameter('like', sprintf('%%%s%%', $filter['query']));
        }

        return $qb;
    }
}
