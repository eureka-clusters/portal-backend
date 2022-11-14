<?php

declare(strict_types=1);

namespace Api\Repository\OAuth;

use Api\Entity;
use Application\Repository\FilteredObjectRepository;
use Application\ValueObject\SearchFormResult;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

use function sprintf;

final class Client extends EntityRepository implements FilteredObjectRepository
{
    public function findFiltered(SearchFormResult $searchFormResult): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('api_entity_oauth_client');
        $qb->from(Entity\OAuth\Client::class, 'api_entity_oauth_client');

        if ($searchFormResult->hasQuery()) {
            $qb->andWhere($qb->expr()->like('api_entity_oauth_client.client', ':like'));
            $qb->setParameter('like', sprintf('%%%s%%', $searchFormResult->getQuery()));
        }

        $direction = $searchFormResult->getDirection();

        switch ($searchFormResult->getOrder()) {
            case 'id':
                $qb->addOrderBy('api_entity_oauth_client.clientId', $direction);
                break;
            case 'name':
                $qb->addOrderBy('api_entity_oauth_client.name', $direction);
                break;
            case 'description':
                $qb->addOrderBy('api_entity_oauth_client.description', $direction);
                break;
            default:
                $qb->addOrderBy('api_entity_oauth_client.clientId', Criteria::ASC);
        }

        return $qb;
    }
}
