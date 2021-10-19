<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Repository\Project\Version;

use Cluster\Entity;
use Doctrine\ORM\EntityRepository;

/**
 *
 */
final class CostsAndEffort extends EntityRepository
{
    public function parseTotalCostsByPartnerAndLatestProjectVersion(
        Entity\Project\Partner $partner,
        Entity\Project\Version $projectVersion
    ): ?float {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('SUM(cluster_entity_project_version_costs_and_effort.costs)');
        $queryBuilder->from(
            Entity\Project\Version\CostsAndEffort::class,
            'cluster_entity_project_version_costs_and_effort'
        );
        $queryBuilder->andWhere('cluster_entity_project_version_costs_and_effort.partner = :partner');
        $queryBuilder->andWhere('cluster_entity_project_version_costs_and_effort.version = :projectVersion');
        $queryBuilder->setParameter('partner', $partner);
        $queryBuilder->setParameter('projectVersion', $projectVersion);
        $queryBuilder->setMaxResults(1);

        return null === $queryBuilder->getQuery()->getOneOrNullResult() ? null : (float)$queryBuilder->getQuery(
        )->getSingleScalarResult();
    }

    public function parseTotalEffortByPartnerAndLatestProjectVersion(
        Entity\Project\Partner $partner,
        Entity\Project\Version $projectVersion
    ): ?float {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('SUM(cluster_entity_project_version_costs_and_effort.effort)');
        $queryBuilder->from(
            Entity\Project\Version\CostsAndEffort::class,
            'cluster_entity_project_version_costs_and_effort'
        );
        $queryBuilder->andWhere('cluster_entity_project_version_costs_and_effort.partner = :partner');
        $queryBuilder->andWhere('cluster_entity_project_version_costs_and_effort.version = :projectVersion');
        $queryBuilder->setParameter('partner', $partner);
        $queryBuilder->setParameter('projectVersion', $projectVersion);
        $queryBuilder->setMaxResults(1);

        return null === $queryBuilder->getQuery()->getOneOrNullResult() ? null : (float)$queryBuilder->getQuery(
        )->getSingleScalarResult();
    }

    public function parseTotalCostsByProjectVersion(Entity\Project\Version $projectVersion): ?float {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('SUM(cluster_entity_project_version_costs_and_effort.costs)');
        $queryBuilder->from(
            Entity\Project\Version\CostsAndEffort::class,
            'cluster_entity_project_version_costs_and_effort'
        );
        $queryBuilder->andWhere('cluster_entity_project_version_costs_and_effort.version = :projectVersion');
        $queryBuilder->setParameter('projectVersion', $projectVersion);
        $queryBuilder->setMaxResults(1);

        return null === $queryBuilder->getQuery()->getOneOrNullResult() ? null : (float)$queryBuilder->getQuery(
        )->getSingleScalarResult();
    }

    public function parseTotalEffortByProjectVersion(Entity\Project\Version $projectVersion): ?float {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('SUM(cluster_entity_project_version_costs_and_effort.effort)');
        $queryBuilder->from(
            Entity\Project\Version\CostsAndEffort::class,
            'cluster_entity_project_version_costs_and_effort'
        );
        $queryBuilder->andWhere('cluster_entity_project_version_costs_and_effort.version = :projectVersion');
        $queryBuilder->setParameter('projectVersion', $projectVersion);
        $queryBuilder->setMaxResults(1);

        return null === $queryBuilder->getQuery()->getOneOrNullResult() ? null : (float)$queryBuilder->getQuery(
        )->getSingleScalarResult();
    }

}
