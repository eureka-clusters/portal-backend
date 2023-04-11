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

final class Service extends EntityRepository implements FilteredObjectRepository
{
    public function findFiltered(SearchFormResult $searchFormResult): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(select: 'api_entity_oauth_service');
        $qb->from(from: Entity\OAuth\Service::class, alias: 'api_entity_oauth_service');

        if ($searchFormResult->hasQuery()) {
            $qb->andWhere($qb->expr()->like(x: 'api_entity_oauth_service.service', y: ':like'));
            $qb->setParameter(key: 'like', value: sprintf('%%%s%%', $searchFormResult->getQuery()));
        }

        $direction = $searchFormResult->getDirection();

        switch ($searchFormResult->getOrder()) {
            case 'id':
                $qb->addOrderBy(sort: 'api_entity_oauth_service.id', order: $direction);
                break;
            case 'service':
                $qb->addOrderBy(sort: 'api_entity_oauth_service.name', order: $direction);
                break;
            default:
                $qb->addOrderBy(sort: 'api_entity_oauth_service.name', order: Criteria::ASC);
        }

        return $qb;
    }
}
