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
        $qb->select('mailing_entity_mailer');
        $qb->from(Entity\Mailer::class, 'mailing_entity_mailer');

        $direction = $searchFormResult->getDirection();

        switch ($searchFormResult->getOrder()) {
            case 'service':
                $qb->addOrderBy('mailing_entity_mailer.service', $direction);
                break;
            case 'name':
                $qb->addOrderBy('mailing_entity_mailer.name', $direction);
                break;
            default:
                $qb->addOrderBy('mailing_entity_mailer.name', Criteria::ASC);
        }

        return $qb;
    }
}
