<?php

declare(strict_types=1);

namespace Cluster\Repository;

use Cluster\Entity;
use Cluster\Entity\Funder;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

use function count;

class ProjectRepository extends EntityRepository
{
    public function getProjectsByFunderAndFilter(Entity\Funder $funder, array $filter)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('cluster_entity_project');
        $queryBuilder->from(Entity\Project::class, 'cluster_entity_project');

        $this->applyFilters($filter, $queryBuilder);

        return $queryBuilder->getQuery()->getResult();
    }

    private function applyFilters(array $filter, QueryBuilder $queryBuilder): void
    {
        //Filters filters filters
        $countryFilter = $filter['country'] ?? [];

        if (! empty($countryFilter)) {
            switch ($filter['country_method']) {
                case 'and':
                    //Find the projects where the country is active
                    $countryFilterSubSelect = $this->_em->createQueryBuilder()
                        ->select('cluster_entity_project_filter_country')
                        ->from(Entity\Project\Partner::class, 'cluster_entity_project_partner_filter_country')
                        ->join('cluster_entity_project_partner_filter_country.project', 'cluster_entity_project_filter_country')
                        ->join(
                            'cluster_entity_project_partner_filter_country.organisation',
                            'cluster_entity_project_partner_filter_country_organisation'
                        )
                        ->join('cluster_entity_project_partner_filter_country_organisation.country', 'cluster_entity_country')
                        ->where(
                            $queryBuilder->expr()->in(
                                'cluster_entity_country.country',
                                $countryFilter
                            )
                        )
                        ->addGroupBy('cluster_entity_project_filter_country.id') //Add an id so we don't get all group by statements
                        ->having(
                            'COUNT(DISTINCT cluster_entity_country) > ' . (count(
                                $countryFilter
                            ) - 1)
                        );

                    $queryBuilder->andWhere(
                        $queryBuilder->expr()->in('cluster_entity_project', $countryFilterSubSelect->getDQL())
                    );

                    break;
                case 'or':
                    //Find the projects where the country is active
                    $countryFilterSubSelect = $this->_em->createQueryBuilder()
                        ->select('cluster_entity_project_filter_country')
                        ->from(Entity\Project\Partner::class, 'cluster_entity_project_partner_filter_country')
                        ->join('cluster_entity_project_partner_filter_country.project', 'cluster_entity_project_filter_country')
                        ->join(
                            'cluster_entity_project_partner_filter_country.organisation',
                            'cluster_entity_project_partner_filter_country_organisation'
                        )
                        ->join('cluster_entity_project_partner_filter_country_organisation.country', 'cluster_entity_country')
                        ->where(
                            $queryBuilder->expr()->in(
                                'cluster_entity_country.country',
                                $countryFilter
                            )
                        );

                    $queryBuilder->andWhere(
                        $queryBuilder->expr()->in('cluster_entity_project', $countryFilterSubSelect->getDQL())
                    );

                    break;
            }
        }

        $organisationTypeFilter = $filter['organisation_type'] ?? [];

        if (! empty($organisationTypeFilter)) {
            switch ($filter['organisation_type_method']) {
                case 'and':
                    //Find the projects we have at least organisations with this type
                    $organisationTypeFilterSubSelect = $this->_em->createQueryBuilder()
                        ->select('cluster_entity_project_filter_organisation_type')
                        ->from(Entity\Project\Partner::class, 'cluster_entity_project_partner_filter_organisation_type')
                        ->join('cluster_entity_project_partner_filter_organisation_type.project', 'cluster_entity_project_filter_organisation_type')
                        ->join(
                            'cluster_entity_project_partner_filter_organisation_type.organisation',
                            'cluster_entity_project_partner_filter_organisation_type_organisation'
                        )
                        ->join('cluster_entity_project_partner_filter_organisation_type_organisation.type', 'cluster_entity_organisation_type')
                        ->where(
                            $queryBuilder->expr()->in(
                                'cluster_entity_organisation_type.type',
                                $organisationTypeFilter
                            )
                        )
                        ->addGroupBy('cluster_entity_project_filter_organisation_type.id')
                        ->having(
                            'COUNT(DISTINCT cluster_entity_organisation_type) > ' . (count(
                                $organisationTypeFilter
                            ) - 1)
                        );

                    $queryBuilder->andWhere(
                        $queryBuilder->expr()->in('cluster_entity_project', $organisationTypeFilterSubSelect->getDQL())
                    );

                    break;
                case 'or':
                    //Find the projects where we have organisations with this type
                    $organisationTypeFilterSubSelect = $this->_em->createQueryBuilder()
                        ->select('cluster_entity_project_filter_organisation_type')
                        ->from(Entity\Project\Partner::class, 'cluster_entity_project_partner_filter_organisation_type')
                        ->join('cluster_entity_project_partner_filter_organisation_type.project', 'cluster_entity_project_filter_organisation_type')
                        ->join(
                            'cluster_entity_project_partner_filter_organisation_type.organisation',
                            'cluster_entity_project_partner_filter_organisation_type_organisation'
                        )
                        ->join('cluster_entity_project_partner_filter_organisation_type_organisation.type', 'cluster_entity_project_partner_filter_organisation_type_organisation_type')
                        ->where(
                            $queryBuilder->expr()->in(
                                'cluster_entity_project_partner_filter_organisation_type_organisation_type.type',
                                $organisationTypeFilter
                            )
                        );

                    $queryBuilder->andWhere(
                        $queryBuilder->expr()->in('cluster_entity_project', $organisationTypeFilterSubSelect->getDQL())
                    );

                    break;
            }
        }

        $projectStatusFilter = $filter['project_status'] ?? [];

        if (! empty($projectStatusFilter)) {
            //Find the projects where we have organisations with this type
            $projectStatusFilterSubSelect = $this->_em->createQueryBuilder()
                ->select('cluster_entity_project_filter_project_status')
                ->from(Entity\Project::class, 'cluster_entity_project_filter_project_status')
                ->join('cluster_entity_project_filter_project_status.status', 'cluster_entity_project_filter_project_status_status')
                ->where(
                    $queryBuilder->expr()->in(
                        'cluster_entity_project_filter_project_status_status.status',
                        $projectStatusFilter
                    )
                );

            $queryBuilder->andWhere(
                $queryBuilder->expr()->in('cluster_entity_project', $projectStatusFilterSubSelect->getDQL())
            );
        }

        $primaryClusterFilter = $filter['primary_cluster'] ?? [];

        if (! empty($primaryClusterFilter)) {
            //Find the projects where we have organisations with this type
            $primaryClusterFilterSubSelect = $this->_em->createQueryBuilder()
                ->select('cluster_entity_project_filter_primary_cluster')
                ->from(Entity\Project::class, 'cluster_entity_project_filter_primary_cluster')
                ->join('cluster_entity_project_filter_primary_cluster.primaryCluster', 'cluster_entity_project_filter_primary_cluster_primary_cluster')
                ->where(
                    $queryBuilder->expr()->in(
                        'cluster_entity_project_filter_primary_cluster_primary_cluster.name',
                        $primaryClusterFilter
                    )
                );

            $queryBuilder->andWhere(
                $queryBuilder->expr()->in('cluster_entity_project', $primaryClusterFilterSubSelect->getDQL())
            );
        }
    }

    public function fetchOrganisationTypes(Funder $funder, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_organisation_type.type',
            $queryBuilder->expr()->countDistinct('cluster_entity_project')
        );

        $queryBuilder->from(Entity\Organisation\Type::class, 'cluster_entity_organisation_type')
            ->join('cluster_entity_organisation_type.organisations', 'cluster_entity_organisation_type_organisations')
            ->join(
                'cluster_entity_organisation_type_organisations.partners',
                'cluster_entity_organisation_type_organisations_partners'
            )
            ->join('cluster_entity_organisation_type_organisations_partners.project', 'cluster_entity_project')
            ->groupBy('cluster_entity_organisation_type');

        $this->applyFunderFilter($queryBuilder, $funder);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    private function applyFunderFilter(QueryBuilder $queryBuilder, Funder $funder): void
    {
        return;

        //Find the projects where the country is active
        $funderSubSelect = $this->_em->createQueryBuilder()
            ->select('cluster_entity_project_funder')
            ->from(Entity\Project::class, 'cluster_entity_project_funder')
            ->join('cluster_entity_project_funder.partners', 'cluster_entity_project_funder_partners')
            ->join(
                'cluster_entity_project_funder_partners.organisation',
                'cluster_entity_project_funder_partners_organisation'
            )
            ->andWhere('cluster_entity_project_funder_partners_organisation.country = :funder_country');

        $queryBuilder->andWhere(
            $queryBuilder->expr()->in('cluster_entity_project', $funderSubSelect->getDQL())
        );
        $queryBuilder->setParameter('funder_country', $funder->getCountry()->getCd());
    }

    public function fetchCountries(Funder $funder, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_project_partners_organisation_country.country',
            $queryBuilder->expr()->countDistinct('cluster_entity_project.id')
        );

        $queryBuilder->from(Entity\Project::class, 'cluster_entity_project')
            ->join('cluster_entity_project.partners', 'cluster_entity_project_partners')
            ->join(
                'cluster_entity_project_partners.organisation',
                'cluster_entity_project_partners_organisation'
            )
            ->join(
                'cluster_entity_project_partners_organisation.country',
                'cluster_entity_project_partners_organisation_country'
            )
            ->groupBy('cluster_entity_project_partners_organisation.country');

        $this->applyFunderFilter($queryBuilder, $funder);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchPrimaryClusters(Funder $funder, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_cluster.name',
            $queryBuilder->expr()->count('cluster_entity_cluster_project_primary.id')
        );

        $queryBuilder->from(Entity\Cluster::class, 'cluster_entity_cluster')
            ->join('cluster_entity_cluster.projectsPrimary', 'cluster_entity_cluster_project_primary')
            ->groupBy('cluster_entity_cluster');

        $this->applyFunderFilter($queryBuilder, $funder);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchProjectStatuses(Funder $funder, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_project_status.status',
            $queryBuilder->expr()->count('cluster_entity_project_status_project.id')
        );

        $queryBuilder->from(Entity\Project\Status::class, 'cluster_entity_project_status')
            ->join('cluster_entity_project_status.projects', 'cluster_entity_project_status_project')
            ->groupBy('cluster_entity_project_status');

        $this->applyFunderFilter($queryBuilder, $funder);

        return $queryBuilder->getQuery()->getArrayResult();
    }
}
