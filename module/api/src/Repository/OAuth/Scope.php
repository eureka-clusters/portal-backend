<?php

declare(strict_types=1);

namespace Api\Repository\OAuth;

use Api\Entity;
use Application\Repository\FilteredObjectRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Application\ValueObject\SearchFormResult;

use function sprintf;

final class Scope extends EntityRepository implements FilteredObjectRepository
{
    public function findFiltered(SearchFormResult $searchFormResult): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('api_entity_oauth_scope');
        $qb->from(Entity\OAuth\Scope::class, 'api_entity_oauth_scope');

        if ($searchFormResult->hasQuery()) {
            $qb->andWhere($qb->expr()->like('api_entity_oauth_scope.scope', ':like'));
            $qb->setParameter('like', sprintf('%%%s%%', $searchFormResult->getQuery()));
        }

        $direction = $searchFormResult->getDirection();

        switch ($searchFormResult->getOrder()) {
            case 'id':
                $qb->addOrderBy('api_entity_oauth_scope.id', $direction);
                break;
            case 'scope':
                $qb->addOrderBy('api_entity_oauth_scope.scope', $direction);
                break;
            default:
                $qb->addOrderBy('api_entity_oauth_scope.scope', Criteria::ASC);
        }

        return $qb;
    }
}
