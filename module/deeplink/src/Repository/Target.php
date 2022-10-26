<?php

declare(strict_types=1);

namespace Deeplink\Repository;

use Deeplink\Entity;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

class Target extends EntityRepository
{
    /**
     * @return Entity\Target[]
     */
    public function findTargets(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(select: 'deeplink_entity_target target');
        $qb->addSelect(select: 'COUNT(deeplink_entity_deeplink) deeplink');
        $qb->from(from: Entity\Target::class, alias: 'deeplink_entity_target');
        $qb->leftJoin(join: 'deeplink_entity_target.deeplink', alias: 'deeplink_entity_deeplink');
        $qb->groupBy(groupBy: 'deeplink_entity_target.id');

        $qb->addOrderBy(sort: 'deeplink_entity_target.target', order: Criteria::ASC);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Entity\Target[]
     */
    public function findTargetsWithRoute(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(select: 'deeplink_entity_target');
        $qb->from(from: Entity\Target::class, alias: 'deeplink_entity_target');
        $qb->andWhere($qb->expr()->isNotNull(x: 'deeplink_entity_target.route'));
        $qb->addOrderBy(sort: 'deeplink_entity_target.target', order: Criteria::ASC);

        return $qb->getQuery()->getResult();
    }
}
