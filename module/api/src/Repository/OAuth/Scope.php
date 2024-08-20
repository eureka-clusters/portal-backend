<?php

declare(strict_types=1);

namespace Api\Repository\OAuth;

use Api\Entity;
use Application\Repository\FilteredObjectRepository;
use Jield\Search\ValueObject\SearchFormResult;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

use function sprintf;

final class Scope extends EntityRepository implements FilteredObjectRepository
{
    #[\Override]
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

        match ($searchFormResult->getOrder()) {
            'id' => $qb->addOrderBy('api_entity_oauth_scope.id', $direction),
            'scope' => $qb->addOrderBy('api_entity_oauth_scope.scope', $direction),
            default => $qb->addOrderBy('api_entity_oauth_scope.scope', \Doctrine\Common\Collections\Order::Ascending->value),
        };

        return $qb;
    }
}
