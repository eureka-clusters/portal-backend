<?php

declare(strict_types=1);

namespace Mailing\Repository;

use Application\Repository\FilteredObjectRepository;
use Jield\Search\ValueObject\SearchFormResult;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Mailing\Entity;

final class Sender extends EntityRepository implements FilteredObjectRepository
{
    #[\Override]
    public function findFiltered(SearchFormResult $searchFormResult): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(select: 'mailing_entity_sender');
        $qb->from(from: Entity\Sender::class, alias: 'mailing_entity_sender');

        $direction = $searchFormResult->getDirection();

        match ($searchFormResult->getOrder()) {
            'sender' => $qb->addOrderBy(sort: 'mailing_entity_sender.sender', order: $direction),
            'email' => $qb->addOrderBy(sort: 'mailing_entity_sender.email', order: $direction),
            'personal' => $qb->addOrderBy(sort: 'mailing_entity_sender.personal', order: $direction),
            default => $qb->addOrderBy(sort: 'mailing_entity_sender.sender', order: \Doctrine\Common\Collections\Order::Ascending->value),
        };

        return $qb;
    }
}
