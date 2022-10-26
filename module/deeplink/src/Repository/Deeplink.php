<?php

declare(strict_types=1);

namespace Deeplink\Repository;

use DateTime;
use Deeplink\Entity;
use Deeplink\Entity\Target;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityRepository;

final class Deeplink extends EntityRepository
{
    public function findActiveDeeplinksByTarget(Target $target): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(select: 'deeplink_entity_deeplink');
        $qb->from(from: Entity\Deeplink::class, alias: 'deeplink_entity_deeplink');

        $qb->andWhere('deeplink_entity_deeplink.endDate >= :today');
        $qb->setParameter(key: 'today', value: new DateTime(), type: Types::DATETIME_MUTABLE);

        $qb->andWhere('deeplink_entity_deeplink.target = :target');
        $qb->setParameter(key: 'target', value: $target);

        $qb->addOrderBy(sort: 'deeplink_entity_deeplink.dateCreated', order: Criteria::ASC);

        return $qb->getQuery()->getResult();
    }

    public function deleteInactiveDeeplinks(): void
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->delete(delete: Entity\Deeplink::class, alias: 'deeplink_entity_deeplink');
        $qb->andWhere('deeplink_entity_deeplink.endDate < :today');
        $qb->setParameter(key: 'today', value: new DateTime(), type: Types::DATETIME_MUTABLE);
        $qb->getQuery()->execute();
    }
}
