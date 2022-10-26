<?php

declare(strict_types=1);

namespace Mailing\Repository;

use Application\Repository\FilteredObjectRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Mailing\Entity;
use Application\ValueObject\SearchFormResult;

final class Sender extends EntityRepository implements FilteredObjectRepository
{
    public function findFiltered(SearchFormResult $searchFormResult): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('mailing_entity_sender');
        $qb->from(Entity\Sender::class, 'mailing_entity_sender');

        $direction = $searchFormResult->getDirection();

        switch ($searchFormResult->getOrder()) {
            case 'sender':
                $qb->addOrderBy('mailing_entity_sender.sender', $direction);
                break;
            case 'email':
                $qb->addOrderBy('mailing_entity_sender.email', $direction);
                break;
            case 'personal':
                $qb->addOrderBy('mailing_entity_sender.personal', $direction);
                break;
            default:
                $qb->addOrderBy('mailing_entity_sender.sender', Criteria::ASC);
        }

        return $qb;
    }
}
