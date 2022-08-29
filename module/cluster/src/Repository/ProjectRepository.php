<?php

declare(strict_types=1);

namespace Cluster\Repository;

use Admin\Entity\User;
use Cluster\Entity\Cluster;
use Cluster\Entity\Country;
use Cluster\Entity\Organisation\Type;
use Cluster\Entity\Project;
use Cluster\Entity\Project\Partner;
use Cluster\Entity\Project\Status;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use DoctrineExtensions\Query\Mysql\MatchAgainst;

use function count;

class ProjectRepository extends EntityRepository
{
    public function getProjectsByUserAndFilter(
        User $user,
        array $filter,
        string $sort = 'project.name',
        string $order = 'asc'
    ): QueryBuilder {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('cluster_entity_project');
        $queryBuilder->from(Project::class, 'cluster_entity_project');

        $this->applyFilters($filter, $queryBuilder);
        $this->applySorting($sort, $order, $queryBuilder);

        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        return $queryBuilder;
    }

    private function applyFilters(array $filter, QueryBuilder $queryBuilder): void
    {
        //Filters filters filters
        $countryFilter = $filter['country'] ?? [];

        if (!empty($countryFilter)) {
            switch ($filter['country_method']) {
                case 'and':
                    //Find the projects where the country is active
                    $countryFilterSubSelect = $this->_em->createQueryBuilder()
                        ->select('cluster_entity_project_filter_country')
                        ->from(Partner::class, 'cluster_entity_project_partner_filter_country')
                        ->join(
                            'cluster_entity_project_partner_filter_country.project',
                            'cluster_entity_project_filter_country'
                        )
                        ->join(
                            'cluster_entity_project_partner_filter_country.organisation',
                            'cluster_entity_project_partner_filter_country_organisation'
                        )
                        ->join(
                            'cluster_entity_project_partner_filter_country_organisation.country',
                            'cluster_entity_country'
                        )
                        ->where(
                            $queryBuilder->expr()->in(
                                'cluster_entity_country.country',
                                $countryFilter
                            )
                        )
                        ->addGroupBy(
                            'cluster_entity_project_filter_country.id'
                        ) //Add an id so we don't get all group by statements
                        ->having(
                            'COUNT(DISTINCT cluster_entity_country) > ' . ((is_countable($countryFilter) ? count(
                                    $countryFilter
                                ) : 0) - 1)
                        );

                    $queryBuilder->andWhere(
                        $queryBuilder->expr()->in('cluster_entity_project', $countryFilterSubSelect->getDQL())
                    );

                    break;
                case 'or':
                    //Find the projects where the country is active
                    $countryFilterSubSelect = $this->_em->createQueryBuilder()
                        ->select('cluster_entity_project_filter_country')
                        ->from(Partner::class, 'cluster_entity_project_partner_filter_country')
                        ->join(
                            'cluster_entity_project_partner_filter_country.project',
                            'cluster_entity_project_filter_country'
                        )
                        ->join(
                            'cluster_entity_project_partner_filter_country.organisation',
                            'cluster_entity_project_partner_filter_country_organisation'
                        )
                        ->join(
                            'cluster_entity_project_partner_filter_country_organisation.country',
                            'cluster_entity_country'
                        )
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

        if (!empty($organisationTypeFilter)) {
            switch ($filter['organisation_type_method']) {
                case 'and':
                    //Find the projects we have at least organisations with this type
                    $organisationTypeFilterSubSelect = $this->_em->createQueryBuilder()
                        ->select('cluster_entity_project_filter_organisation_type')
                        ->from(Partner::class, 'cluster_entity_project_partner_filter_organisation_type')
                        ->join(
                            'cluster_entity_project_partner_filter_organisation_type.project',
                            'cluster_entity_project_filter_organisation_type'
                        )
                        ->join(
                            'cluster_entity_project_partner_filter_organisation_type.organisation',
                            'cluster_entity_project_partner_filter_organisation_type_organisation'
                        )
                        ->join(
                            'cluster_entity_project_partner_filter_organisation_type_organisation.type',
                            'cluster_entity_organisation_type'
                        )
                        ->where(
                            $queryBuilder->expr()->in(
                                'cluster_entity_organisation_type.type',
                                $organisationTypeFilter
                            )
                        )
                        ->addGroupBy('cluster_entity_project_filter_organisation_type.id')
                        ->having(
                            'COUNT(DISTINCT cluster_entity_organisation_type) > ' . ((is_countable(
                                    $organisationTypeFilter
                                ) ? count(
                                    $organisationTypeFilter
                                ) : 0) - 1)
                        );

                    $queryBuilder->andWhere(
                        $queryBuilder->expr()->in('cluster_entity_project', $organisationTypeFilterSubSelect->getDQL())
                    );

                    break;
                case 'or':
                    //Find the projects where we have organisations with this type
                    $organisationTypeFilterSubSelect = $this->_em->createQueryBuilder()
                        ->select('cluster_entity_project_filter_organisation_type')
                        ->from(Partner::class, 'cluster_entity_project_partner_filter_organisation_type')
                        ->join(
                            'cluster_entity_project_partner_filter_organisation_type.project',
                            'cluster_entity_project_filter_organisation_type'
                        )
                        ->join(
                            'cluster_entity_project_partner_filter_organisation_type.organisation',
                            'cluster_entity_project_partner_filter_organisation_type_organisation'
                        )
                        ->join(
                            'cluster_entity_project_partner_filter_organisation_type_organisation.type',
                            'cluster_entity_project_partner_filter_organisation_type_organisation_type'
                        )
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

        if (!empty($projectStatusFilter)) {
            //Find the projects where we have organisations with this type
            $projectStatusFilterSubSelect = $this->_em->createQueryBuilder()
                ->select('cluster_entity_project_filter_project_status')
                ->from(Project::class, 'cluster_entity_project_filter_project_status')
                ->join(
                    'cluster_entity_project_filter_project_status.status',
                    'cluster_entity_project_filter_project_status_status'
                )
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

        $programmeCallFilter = $filter['programme_call'] ?? [];

        if (!empty($programmeCallFilter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in('cluster_entity_project.programmeCall', $programmeCallFilter)
            );
        }

        $clustersFilter = $filter['clusters'] ?? [];
        if (!empty($clustersFilter)) {
            //Find the projects where we have organisations with this type
            $primaryClusterFilterSubSelect = $this->_em->createQueryBuilder()
                ->select('cluster_entity_project_filter_primary_cluster')
                ->from(Project::class, 'cluster_entity_project_filter_primary_cluster')
                ->join(
                    'cluster_entity_project_filter_primary_cluster.primaryCluster',
                    'cluster_entity_project_filter_primary_cluster_primary_cluster'
                )
                ->where(
                    $queryBuilder->expr()->in(
                        'cluster_entity_project_filter_primary_cluster_primary_cluster.name',
                        $clustersFilter
                    )
                );

            $secondaryClusterFilterSubSelect = $this->_em->createQueryBuilder()
                ->select('cluster_entity_project_filter_secondary_cluster')
                ->from(Project::class, 'cluster_entity_project_filter_secondary_cluster')
                ->join(
                    'cluster_entity_project_filter_secondary_cluster.secondaryCluster',
                    'cluster_entity_project_filter_secondary_cluster_secondary_cluster'
                )
                ->where(
                    $queryBuilder->expr()->in(
                        'cluster_entity_project_filter_secondary_cluster_secondary_cluster.name',
                        $clustersFilter
                    )
                );

            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->in('cluster_entity_project', $primaryClusterFilterSubSelect->getDQL()),
                    $queryBuilder->expr()->in('cluster_entity_project', $secondaryClusterFilterSubSelect->getDQL()),
                )
            );
        }
    }

    private function applySorting(string $sort, string $order, QueryBuilder $queryBuilder): void
    {
        $sortColumn = null;

        switch ($sort) {
            case 'project.number':
                $sortColumn = 'cluster_entity_project.number';
                break;
            case 'project.name':
                $sortColumn = 'cluster_entity_project.name';
                // $queryBuilder->join('project_partner.project', 'project');
                break;
            case 'project.primaryCluster.name':
                $sortColumn = 'primaryCluster.name';
                $queryBuilder->join('cluster_entity_project.primaryCluster', 'primaryCluster');
                break;
            case 'project.secondaryCluster.name':
                $sortColumn = 'secondaryCluster.name';
                $queryBuilder->leftJoin('cluster_entity_project.secondaryCluster', 'secondaryCluster');
                break;
            case 'project.status.status':
                $sortColumn = 'projectStatus.status';
                $queryBuilder->join('cluster_entity_project.status', 'projectStatus');
                break;

            //todo: if the lastest version column always only displays "latest" then sorting doesn't make sense
            case 'project.latestVersion.type.type':
                $sortColumn = 'latestversion_type.type';
                $queryBuilder->leftJoin(
                    'cluster_entity_project.versions',
                    'latestversion',
                    'WITH',
                    'latestversion.type = 3'
                );
                $queryBuilder->join('latestversion.type', 'latestversion_type');
                break;

            //todo how can the id of the latest version type be selected dynamically? or is this a fixed id
            case 'project.latestVersionTotalCosts':
                $sortColumn = 'latestversion.costs';
                $queryBuilder->leftJoin(
                    'cluster_entity_project.versions',
                    'latestversion',
                    'WITH',
                    'latestversion.type = 3'
                );
                break;
            case 'project.latestVersionTotalEffort':
                $sortColumn = 'latestversion.effort';
                $queryBuilder->leftJoin(
                    'cluster_entity_project.versions',
                    'latestversion',
                    'WITH',
                    'latestversion.type = 3'
                );
                break;
        }

        // var_dump($sortColumn);
        // var_dump($sort);
        // var_dump($order);
        // die();

        if (isset($sortColumn)) {
            $queryBuilder->orderBy($sortColumn, $order);
        }
    }

    private function applyUserFilter(QueryBuilder $queryBuilder, User $user): void
    {
        //Short-circuit this function when the user is member of eureka secretariat
        if ($user->isEurekaSecretariatStaffMember()) {
            return;
        }

        //Find the projects where the country is active
        $funderSubSelect = $this->_em->createQueryBuilder()
            ->select('cluster_entity_project_funder')
            ->from(Project::class, 'cluster_entity_project_funder')
            ->join('cluster_entity_project_funder.partners', 'cluster_entity_project_funder_partners')
            ->join(
                'cluster_entity_project_funder_partners.organisation',
                'cluster_entity_project_funder_partners_organisation'
            )
            ->andWhere('cluster_entity_project_funder_partners_organisation.country = :funder_country');

        $queryBuilder->andWhere(
            $queryBuilder->expr()->in('cluster_entity_project', $funderSubSelect->getDQL())
        );

        //Create an empty country to have a valid query
        $country = new Country();

        //When the user is a funder we can use the country of the funder
        if ($user->isFunder()) {
            $country = $user->getFunder()?->getCountry();
        }

        $queryBuilder->setParameter('funder_country', $country);
    }

    public function searchProjects(
        User $user,
        string $query,
        int $limit
    ): QueryBuilder {
        $config = $this->_em->getConfiguration();
        $config->addCustomStringFunction('match_against', MatchAgainst::class);

        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('cluster_entity_project');
        $queryBuilder->from(Project::class, 'cluster_entity_project');

        $queryBuilder->addSelect(
            'MATCH_AGAINST (cluster_entity_project.number, cluster_entity_project.name, cluster_entity_project.title, cluster_entity_project.description) AGAINST (:match IN BOOLEAN MODE) as score'
        );
        $queryBuilder->andWhere(
            'MATCH_AGAINST (cluster_entity_project.number, cluster_entity_project.name, cluster_entity_project.title, cluster_entity_project.description) AGAINST (:match IN BOOLEAN MODE) > 0'
        );
        $queryBuilder->setParameter('match', $query);

        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        $queryBuilder->setMaxResults($limit);

        return $queryBuilder;
    }

    public function findProjectBySlugAndUser(string $slug, User $user): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('cluster_entity_project');
        $queryBuilder->from(Project::class, 'cluster_entity_project');

        $queryBuilder->andWhere('cluster_entity_project.slug = :slug')->setParameter('slug', $slug);

        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        return $queryBuilder;
    }

    public function fetchOrganisationTypes(User $user, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_organisation_type.type',
            $queryBuilder->expr()->countDistinct('cluster_entity_project')
        );

        $queryBuilder->from(Type::class, 'cluster_entity_organisation_type')
            ->join('cluster_entity_organisation_type.organisations', 'cluster_entity_organisation_type_organisations')
            ->join(
                'cluster_entity_organisation_type_organisations.partners',
                'cluster_entity_organisation_type_organisations_partners'
            )
            ->join('cluster_entity_organisation_type_organisations_partners.project', 'cluster_entity_project')
            ->groupBy('cluster_entity_organisation_type');

        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchCountries(User $user, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_project_partners_organisation_country.country',
            $queryBuilder->expr()->countDistinct('cluster_entity_project.id')
        );

        $queryBuilder->from(Project::class, 'cluster_entity_project')
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

        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchProgrammeCalls(User $user, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_project.programmeCall',
            $queryBuilder->expr()->count('cluster_entity_project.id')
        );

        $queryBuilder->from(Project::class, 'cluster_entity_project')
            ->groupBy('cluster_entity_project.programmeCall');

        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchClusters(): array
    {
        // it should be a left join so that all clusters are returned even with 0 projects
        $queryBuilder = $this->_em->createQueryBuilder();

        // select primary
        $queryBuilder->select(
            'cluster_entity_cluster.name',
            $queryBuilder->expr()->count('cluster_entity_project.id'),
        );

        $queryBuilder->from(Cluster::class, 'cluster_entity_cluster')
            ->leftJoin('cluster_entity_cluster.projectsPrimary', 'cluster_entity_project')
            ->groupBy('cluster_entity_cluster')
            ->orderBy('cluster_entity_cluster.name', Criteria::ASC);

        $primaryClusters = $queryBuilder->getQuery()->getArrayResult();

        // select secondary
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select(
            'cluster_entity_cluster.name',
            $queryBuilder->expr()->count('cluster_entity_project.id'),
        );

        $queryBuilder->from(Cluster::class, 'cluster_entity_cluster')
            ->leftJoin('cluster_entity_cluster.projectsSecondary', 'cluster_entity_project')
            ->groupBy('cluster_entity_cluster')
            ->orderBy('cluster_entity_cluster.name', Criteria::ASC);

        $secondaryClusters = $queryBuilder->getQuery()->getArrayResult();

        return array_map(static fn(array $cluster1, $cluster2) => [
            'name' => $cluster1['name'],
            '1' => $cluster1[1],
            '2' => $cluster2[1],
        ], $primaryClusters, $secondaryClusters);
    }

    public function fetchProjectStatuses(User $user, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_project_status.status',
            $queryBuilder->expr()->count('cluster_entity_project.id')
        );
        $queryBuilder->from(Status::class, 'cluster_entity_project_status')
            ->join('cluster_entity_project_status.projects', 'cluster_entity_project')
            ->groupBy('cluster_entity_project_status');

        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        return $queryBuilder->getQuery()->getArrayResult();
    }
}
