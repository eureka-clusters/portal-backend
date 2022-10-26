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
        $qb->select('deeplink_entity_deeplink');
        $qb->from(Entity\Deeplink::class, 'deeplink_entity_deeplink');

        $qb->andWhere('deeplink_entity_deeplink.endDate >= :today');
        $qb->setParameter('today', new DateTime(), Types::DATETIME_MUTABLE);

        $qb->andWhere('deeplink_entity_deeplink.target = :target');
        $qb->setParameter('target', $target);

        $qb->addOrderBy('deeplink_entity_deeplink.dateCreated', Criteria::ASC);

        return $qb->getQuery()->getResult();
    }

    public function deleteInactiveDeeplinks(): void
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->delete(Entity\Deeplink::class, 'deeplink_entity_deeplink');
        $qb->andWhere('deeplink_entity_deeplink.endDate < :today');
        $qb->setParameter('today', new DateTime(), Types::DATETIME_MUTABLE);
        $qb->getQuery()->execute();
    }
}
