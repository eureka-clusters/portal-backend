<?php

declare(strict_types=1);

namespace Mailing\Repository;

use Application\Repository\FilteredObjectRepository;
use Application\ValueObject\SearchFormResult;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Mailing\Entity;

final class Sender extends EntityRepository implements FilteredObjectRepository
{
    public function findFiltered(SearchFormResult $searchFormResult): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(select: 'mailing_entity_sender');
        $qb->from(from: Entity\Sender::class, alias: 'mailing_entity_sender');

        $direction = $searchFormResult->getDirection();

        switch ($searchFormResult->getOrder()) {
            case 'sender':
                $qb->addOrderBy(sort: 'mailing_entity_sender.sender', order: $direction);
                break;
            case 'email':
                $qb->addOrderBy(sort: 'mailing_entity_sender.email', order: $direction);
                break;
            case 'personal':
                $qb->addOrderBy(sort: 'mailing_entity_sender.personal', order: $direction);
                break;
            default:
                $qb->addOrderBy(sort: 'mailing_entity_sender.sender', order: Criteria::ASC);
        }

        return $qb;
    }
}
