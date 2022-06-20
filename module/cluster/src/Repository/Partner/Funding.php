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
        $qb->select('cluster_partner_funding');
        $qb->from(Partner\Funding::class, 'cluster_partner_funding');

        $qb->andWhere('cluster_partner_funding.year = :year');
        $qb->andWhere('cluster_partner_funding.partner = :partner');

        $qb->setParameter('partner', $partner);
        $qb->setParameter('year', $year);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
