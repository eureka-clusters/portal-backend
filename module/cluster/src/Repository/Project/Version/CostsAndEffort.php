<?php

declare(strict_types=1);

namespace Cluster\Repository\Project\Version;

use Cluster\Entity;
use Cluster\Entity\Project\Partner;
use Cluster\Entity\Project\Version;
use Doctrine\ORM\EntityRepository;

final class CostsAndEffort extends EntityRepository
{

    public function findTotalCostsByPartnerAndLatestProjectVersionAndYear(
        Partner $partner,
        Version $projectVersion,
        int $year
    ): float {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('cluster_entity_project_version_costs_and_effort.costs');
        $queryBuilder->from(
            Entity\Project\Version\CostsAndEffort::class,
            'cluster_entity_project_version_costs_and_effort'
        );
        $queryBuilder->andWhere('cluster_entity_project_version_costs_and_effort.partner = :partner');
        $queryBuilder->andWhere('cluster_entity_project_version_costs_and_effort.version = :projectVersion');
        $queryBuilder->andWhere('cluster_entity_project_version_costs_and_effort.year = :year');
        $queryBuilder->setParameter('partner', $partner);
        $queryBuilder->setParameter('projectVersion', $projectVersion);
        $queryBuilder->setParameter('year', $year);

        return null === $queryBuilder->getQuery()->getOneOrNullResult() ? 0.0 : (float)$queryBuilder->getQuery(
        )->getSingleScalarResult();
    }

    public function findTotalEffortByPartnerAndLatestProjectVersionAndYear(
        Partner $partner,
        Version $projectVersion,
        int $year
    ): float {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('cluster_entity_project_version_costs_and_effort.effort');
        $queryBuilder->from(
            Entity\Project\Version\CostsAndEffort::class,
            'cluster_entity_project_version_costs_and_effort'
        );
        $queryBuilder->andWhere('cluster_entity_project_version_costs_and_effort.partner = :partner');
        $queryBuilder->andWhere('cluster_entity_project_version_costs_and_effort.version = :projectVersion');
        $queryBuilder->andWhere('cluster_entity_project_version_costs_and_effort.year = :year');
        $queryBuilder->setParameter('partner', $partner);
        $queryBuilder->setParameter('projectVersion', $projectVersion);
        $queryBuilder->setParameter('year', $year);

        return null === $queryBuilder->getQuery()->getOneOrNullResult() ? 0.0 : (float)$queryBuilder->getQuery(
        )->getSingleScalarResult();
    }

    public function parseTotalCostsByProjectVersion(Version $projectVersion): float
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('SUM(cluster_entity_project_version_costs_and_effort.costs)');
        $queryBuilder->from(
            Entity\Project\Version\CostsAndEffort::class,
            'cluster_entity_project_version_costs_and_effort'
        );
        $queryBuilder->andWhere('cluster_entity_project_version_costs_and_effort.version = :projectVersion');
        $queryBuilder->setParameter('projectVersion', $projectVersion);
        $queryBuilder->setMaxResults(1);

        return null === $queryBuilder->getQuery()->getOneOrNullResult() ? 0.0 : (float)$queryBuilder->getQuery(
        )->getSingleScalarResult();
    }

    public function parseTotalEffortByProjectVersion(Version $projectVersion): float
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('SUM(cluster_entity_project_version_costs_and_effort.effort)');
        $queryBuilder->from(
            Entity\Project\Version\CostsAndEffort::class,
            'cluster_entity_project_version_costs_and_effort'
        );
        $queryBuilder->andWhere('cluster_entity_project_version_costs_and_effort.version = :projectVersion');
        $queryBuilder->setParameter('projectVersion', $projectVersion);
        $queryBuilder->setMaxResults(1);

        return null === $queryBuilder->getQuery()->getOneOrNullResult() ? 0.0 : (float)$queryBuilder->getQuery(
        )->getSingleScalarResult();
    }
}
