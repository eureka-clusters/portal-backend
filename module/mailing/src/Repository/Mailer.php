<?php

declare(strict_types=1);

namespace Mailing\Repository;

use Application\Repository\FilteredObjectRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Mailing\Entity;
use Application\ValueObject\SearchFormResult;

final class Mailer extends EntityRepository implements FilteredObjectRepository
{
    public function findFiltered(SearchFormResult $searchFormResult): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(select: 'mailing_entity_mailer');
        $qb->from(from: Entity\Mailer::class, alias: 'mailing_entity_mailer');

        $direction = $searchFormResult->getDirection();

        switch ($searchFormResult->getOrder()) {
            case 'service':
                $qb->addOrderBy(sort: 'mailing_entity_mailer.service', order: $direction);
                break;
            case 'name':
                $qb->addOrderBy(sort: 'mailing_entity_mailer.name', order: $direction);
                break;
            default:
                $qb->addOrderBy(sort: 'mailing_entity_mailer.name', order: Criteria::ASC);
        }

        return $qb;
    }
}
