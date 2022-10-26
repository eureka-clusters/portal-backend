<?php

declare(strict_types=1);

namespace Cluster\Repository\Partner;

use Cluster\Entity\Project\Partner;
use Doctrine\ORM\EntityRepository;

final class Funding extends EntityRepository
{
    public function findFundingByPartnerAndYear(
        Partner $partner,
        int $year
    ): ?Partner\Funding {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(select: 'cluster_partner_funding');
        $qb->from(from: Partner\Funding::class, alias: 'cluster_partner_funding');

        $qb->andWhere('cluster_partner_funding.year = :year');
        $qb->andWhere('cluster_partner_funding.partner = :partner');

        $qb->setParameter(key: 'partner', value: $partner);
        $qb->setParameter(key: 'year', value: $year);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
