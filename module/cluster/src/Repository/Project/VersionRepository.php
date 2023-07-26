<?php

declare(strict_types=1);

namespace Cluster\Repository\Project;

use Admin\Entity\User;
use Cluster\Entity\Cluster;
use Cluster\Entity\Country;
use Cluster\Entity\Project;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Jield\Search\ValueObject\SearchFormResult;

class VersionRepository extends EntityRepository
{
    public function getVersionsByFilter(
        User             $user,
        SearchFormResult $searchFormResult,
    ): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select(select: 'cluster_entity_project_version');
        $queryBuilder->from(from: Project\Version::class, alias: 'cluster_entity_project_version');

        //We always need a join on project
        $queryBuilder->join(join: 'cluster_entity_project_version.project', alias: 'cluster_entity_project');

        //Sort on the submission date
        $queryBuilder->orderBy(sort: 'cluster_entity_project_version.submissionDate', order: Criteria::ASC);

        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        return $queryBuilder;
    }

    private function applyUserFilter(QueryBuilder $queryBuilder, User $user): void
    {
        /** Short-circuit the function when the user is member of Eureka Secretariat */
        if ($user->isEurekaSecretariatStaffMember()) {
            return;
        }

        //Find the projects where the country is active
        $funderSubSelect = $this->_em->createQueryBuilder()
            ->select(select: 'cluster_entity_project_funder')
            ->from(from: Project::class, alias: 'cluster_entity_project_funder')
            ->join(join: 'cluster_entity_project_funder.partners', alias: 'cluster_entity_project_funder_partners')
            ->join(
                join: 'cluster_entity_project_funder_partners.organisation',
                alias: 'cluster_entity_project_funder_partners_organisation'
            )
            ->andWhere('cluster_entity_project_funder_partners_organisation.country = :funder_country');

        $queryBuilder->andWhere(
            $queryBuilder->expr()->in(x: 'cluster_entity_project', y: $funderSubSelect->getDQL())
        );

        //Create an empty country to have a valid query which will not give any result
        $country = (new Country())->setId(id: 0);

        //When the user is a funder we can use the country of the funder
        if ($user->isFunder()) {
            $country = $user->getFunder()?->getCountry();
        }

        $queryBuilder->setParameter(key: 'funder_country', value: $country);
    }
}
