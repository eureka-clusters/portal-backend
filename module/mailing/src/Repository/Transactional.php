<?php

declare(strict_types=1);

namespace Mailing\Repository;

use Application\Repository\FilteredObjectRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Mailing\Entity;
use Application\ValueObject\SearchFormResult;

use function sprintf;

final class Transactional extends EntityRepository implements FilteredObjectRepository
{
    public function findFiltered(SearchFormResult $searchFormResult): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('mailing_entity_transactional');
        $qb->from(Entity\Transactional::class, 'mailing_entity_transactional');

        $qb = $this->applyTransactionalFilter($qb, $searchFormResult);

        $direction = $searchFormResult->getDirection();

        switch ($searchFormResult->getOrder()) {
            case 'id':
                $qb->addOrderBy('mailing_entity_transactional.id', $direction);
                break;
            case 'key':
                $qb->addOrderBy('mailing_entity_transactional.key', $direction);
                break;
            case 'transactional':
                $qb->addOrderBy('mailing_entity_transactional.name', $direction);
                break;
            case 'subject':
                $qb->addOrderBy('mailing_entity_transactional.mailSubject', $direction);
                break;
            case 'last-update':
                $qb->addOrderBy('mailing_entity_transactional.lastUpdate', $direction);
                break;
            default:
                $qb->addOrderBy('mailing_entity_transactional.name', Criteria::ASC);
        }

        return $qb;
    }

    public function applyTransactionalFilter(QueryBuilder $qb, SearchFormResult $searchFormResult): QueryBuilder
    {
        if ($searchFormResult->hasQuery()) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('mailing_entity_transactional.name', ':like'),
                    $qb->expr()->like('mailing_entity_transactional.key', ':like'),
                    $qb->expr()->like('mailing_entity_transactional.mailSubject', ':like'),
                )
            );

            $qb->setParameter('like', sprintf('%%%s%%', $searchFormResult->getQuery()));
        }

        if ($searchFormResult->hasFilterByKey('locked')) {
            $qb->andWhere(
                $qb->expr()->in('mailing_entity_transactional.key', Entity\Transactional::$lockedKeys)
            );
        }

        return $qb;
    }
}
