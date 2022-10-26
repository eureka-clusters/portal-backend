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
        $qb->select(select: 'mailing_entity_transactional');
        $qb->from(from: Entity\Transactional::class, alias: 'mailing_entity_transactional');

        $qb = $this->applyTransactionalFilter(qb: $qb, searchFormResult: $searchFormResult);

        $direction = $searchFormResult->getDirection();

        switch ($searchFormResult->getOrder()) {
            case 'id':
                $qb->addOrderBy(sort: 'mailing_entity_transactional.id', order: $direction);
                break;
            case 'key':
                $qb->addOrderBy(sort: 'mailing_entity_transactional.key', order: $direction);
                break;
            case 'transactional':
                $qb->addOrderBy(sort: 'mailing_entity_transactional.name', order: $direction);
                break;
            case 'subject':
                $qb->addOrderBy(sort: 'mailing_entity_transactional.mailSubject', order: $direction);
                break;
            case 'last-update':
                $qb->addOrderBy(sort: 'mailing_entity_transactional.lastUpdate', order: $direction);
                break;
            default:
                $qb->addOrderBy(sort: 'mailing_entity_transactional.name', order: Criteria::ASC);
        }

        return $qb;
    }

    public function applyTransactionalFilter(QueryBuilder $qb, SearchFormResult $searchFormResult): QueryBuilder
    {
        if ($searchFormResult->hasQuery()) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like(x: 'mailing_entity_transactional.name', y: ':like'),
                    $qb->expr()->like(x: 'mailing_entity_transactional.key', y: ':like'),
                    $qb->expr()->like(x: 'mailing_entity_transactional.mailSubject', y: ':like'),
                )
            );

            $qb->setParameter(key: 'like', value: sprintf('%%%s%%', $searchFormResult->getQuery()));
        }

        if ($searchFormResult->hasFilterByKey(key: 'locked')) {
            $qb->andWhere(
                $qb->expr()->in(x: 'mailing_entity_transactional.key', y: Entity\Transactional::$lockedKeys)
            );
        }

        return $qb;
    }
}
