<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Repository\Project;

use Cluster\Entity;
use Cluster\Entity\Funder;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

/**
 *
 */
class PartnerRepository extends EntityRepository
{
    public function getPartnersByFunderAndFilter(Entity\Funder $funder, array $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('cluster_entity_project_partner');
        $queryBuilder->from(Entity\Project\Partner::class, 'cluster_entity_project_partner');


        return $queryBuilder->getQuery()->getResult();
    }

    public function getPartnersByProject(Entity\Project $project): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('cluster_entity_project_partner');
        $queryBuilder->from(Entity\Project\Partner::class, 'cluster_entity_project_partner');
        $queryBuilder->join('cluster_entity_project_partner.organisation','cluster_entity_organisation');
        $queryBuilder->where('cluster_entity_project_partner.project = :project');
        $queryBuilder->setParameter('project', $project);
        $queryBuilder->addOrderBy('cluster_entity_organisation.name');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchCountries(Funder $funder, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_country.country',
            $queryBuilder->expr()->count('cluster_entity_project_partner.id')
        );

        $queryBuilder->from(Entity\Project\Partner::class, 'cluster_entity_project_partner');
        $queryBuilder->join('cluster_entity_project_partner.organisation', 'cluster_entity_organisation');
        $queryBuilder->join('cluster_entity_organisation.country', 'cluster_entity_country');
        $queryBuilder->groupBy('cluster_entity_country');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchOrganisationTypes(Funder $funder, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_organisation_type.type',
            $queryBuilder->expr()->count('cluster_entity_organisation_partners.id')
        );

        $queryBuilder->from(Entity\Organisation\Type::class, 'cluster_entity_organisation_type');
        $queryBuilder->join('cluster_entity_organisation_type.organisations', 'cluster_entity_organisation');
        $queryBuilder->join('cluster_entity_organisation.partners', 'cluster_entity_organisation_partners');
        $queryBuilder->groupBy('cluster_entity_organisation_type');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchPrimaryClusters(Funder $funder, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_cluster.name',
            $queryBuilder->expr()->count('cluster_entity_cluster_project_primary_partner.id')
        );

        $queryBuilder->from(Entity\Cluster::class, 'cluster_entity_cluster');
        $queryBuilder->join('cluster_entity_cluster.projectsPrimary', 'cluster_entity_cluster_project_primary');
        $queryBuilder->join(
            'cluster_entity_cluster_project_primary.partners',
            'cluster_entity_cluster_project_primary_partner'
        );

        $queryBuilder->groupBy('cluster_entity_cluster');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchProjectStatuses(Funder $funder, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_project_status.status',
            $queryBuilder->expr()->count('cluster_entity_project_status_project_partners.id')
        );

        $queryBuilder->from(Entity\Project\Status::class, 'cluster_entity_project_status');
        $queryBuilder->join('cluster_entity_project_status.projects', 'cluster_entity_project_status_project');
        $queryBuilder->join(
            'cluster_entity_project_status_project.partners',
            'cluster_entity_project_status_project_partners'
        );

        $queryBuilder->groupBy('cluster_entity_project_status');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchYears(Funder $funder): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select('cluster_entity_project_version_costs_and_effort.year')
            ->distinct(true)
            ->from(Entity\Project\Version\CostsAndEffort::class, 'cluster_entity_project_version_costs_and_effort')
            ->orderBy('cluster_entity_project_version_costs_and_effort.year', Criteria::ASC);

        return $queryBuilder->getQuery()->getArrayResult();
    }

}
