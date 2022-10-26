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
        $qb->select('deeplink_entity_target target');
        $qb->addSelect('COUNT(deeplink_entity_deeplink) deeplink');
        $qb->from(Entity\Target::class, 'deeplink_entity_target');
        $qb->leftJoin('deeplink_entity_target.deeplink', 'deeplink_entity_deeplink');
        $qb->groupBy('deeplink_entity_target.id');

        $qb->addOrderBy('deeplink_entity_target.target', Criteria::ASC);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Entity\Target[]
     */
    public function findTargetsWithRoute(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('deeplink_entity_target');
        $qb->from(Entity\Target::class, 'deeplink_entity_target');
        $qb->andWhere($qb->expr()->isNotNull('deeplink_entity_target.route'));
        $qb->addOrderBy('deeplink_entity_target.target', Criteria::ASC);

        return $qb->getQuery()->getResult();
    }
}
