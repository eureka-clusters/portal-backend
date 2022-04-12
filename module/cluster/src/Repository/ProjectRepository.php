<?php

declare(strict_types=1);

namespace Cluster\Repository;

use Cluster\Entity\Cluster;
use Cluster\Entity\Funder;
use Cluster\Entity\Organisation\Type;
use Cluster\Entity\Project;
use Cluster\Entity\Organisation;
use Cluster\Entity\Project\Partner;
use Cluster\Entity\Project\Status;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use DoctrineExtensions\Query\Mysql\MatchAgainst;

use function count;

class ProjectRepository extends EntityRepository
{
    public function getProjectsByFunderAndFilter(
        Funder $funder,
        array $filter,
        string $sort = 'project.name',
        string $order = 'asc'
    ): QueryBuilder {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('cluster_entity_project');
        $queryBuilder->from(Project::class, 'cluster_entity_project');

        $this->applyFilters($filter, $queryBuilder);
        $this->applySorting($sort, $order, $queryBuilder);
        $this->applyFunderFilter($queryBuilder, $funder);

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

    private function applyFunderFilter(QueryBuilder $queryBuilder, Funder $funder): void
    {
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
        $queryBuilder->setParameter('funder_country', $funder->getCountry());
    }


    /*
    tests with queryBuilders and get result as array
     */
    public function searchTest1(
        Funder $funder,
        string $queryParam,
        int $limit
    ) {
        // queryBuilder for Project
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->addSelect('cluster_entity_project.slug slug, cluster_entity_project.name name, cluster_entity_project.title title, cluster_entity_project.description description');
        $queryBuilder->addSelect("'project' AS type");
        $queryBuilder->addSelect("MATCH_AGAINST (cluster_entity_project.number, cluster_entity_project.name, cluster_entity_project.title, cluster_entity_project.description) AGAINST (:match IN BOOLEAN MODE) AS score");
        $queryBuilder->from(Project::class, 'cluster_entity_project');
        $queryBuilder->andWhere('MATCH_AGAINST (cluster_entity_project.number, cluster_entity_project.name, cluster_entity_project.title, cluster_entity_project.description) AGAINST (:match IN BOOLEAN MODE) > 0');
        $queryBuilder->setParameter('match', $queryParam);

        $result = $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        // echo "\n Result: \n";
        // var_dump($result);

        // echo "\n SQL: \n";
        // $query=$queryBuilder->getQuery();
        // // SHOW SQL:
        // echo $query->getSQL();

        // echo "\n Parameters: \n";
        // // Show Parameters:
        // echo $query->getParameters();
        // echo "\n";

        // queryBuilder for Organisation
        $queryBuilder2 = $this->_em->createQueryBuilder();
        $queryBuilder2->addSelect('cluster_entity_organisation.slug slug, cluster_entity_organisation.name name, cluster_entity_organisation.name title');

        // it doesn't work to have "NULL" columns
        // See: https://github.com/doctrine/orm/issues/1670

        // $queryBuilder2->addSelect('"" AS description'); // doesn't work
        // $queryBuilder2->addSelect('FALSE AS description'); // doesn't work
        // $queryBuilder2->addSelect('NULL AS description'); // doesn't work
        $queryBuilder2->addSelect('NULLIF(1, 1) AS description'); // works description is then null

        $queryBuilder2->addSelect("'organisation' AS type");
        $queryBuilder2->addSelect("MATCH_AGAINST (cluster_entity_organisation.name) AGAINST (:match IN BOOLEAN MODE) AS score");
        $queryBuilder2->from(Organisation::class, 'cluster_entity_organisation');
        $queryBuilder2->andWhere('MATCH_AGAINST (cluster_entity_organisation.name) AGAINST (:match IN BOOLEAN MODE) > 0');
        $queryBuilder2->setParameter('match', $queryParam);

        $result2 = $queryBuilder2->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        echo "\n Result: \n";
        var_dump($result2);

        // echo "\n SQL: \n";
        // $query=$queryBuilder2->getQuery();
        // // SHOW SQL:
        // echo $query->getSQL();

        // echo "\n Parameters: \n";
        // // Show Parameters:
        // echo $query->getParameters();
        // echo "\n";

        $combinedResult = array_merge($result, $result2);

        // sort the combined array after score
        $score = array_column($combinedResult, 'score');
        array_multisort($score, SORT_DESC, $combinedResult);

        echo "\n combinedResult after sort: \n";
        var_dump($combinedResult);

        die('searchTest1');
    }


    // manual sql
    public function searchTest2(
        Funder $funder,
        string $queryParam,
        int $limit
    ) {

        $SQL=<<<EOP
        SELECT
            c0_.slug AS slug,
            c0_.name AS name,
            c0_.title AS title,
            c0_.description AS description,
            'project' AS type,
            MATCH (c0_.number, c0_.name, c0_.title, c0_.description) AGAINST ('test' IN BOOLEAN MODE) as score
        FROM cluster_project c0_
            WHERE MATCH (c0_.number, c0_.name, c0_.title, c0_.description) AGAINST ('test' IN BOOLEAN MODE) > 0

        UNION

        SELECT
            c1_.slug AS slug,
            c1_.name AS name,
            c1_.name AS title,
            NULLIF(1, 1) AS description,
            'organisation' AS type,
            MATCH (c1_.name) AGAINST ('test' IN BOOLEAN MODE) as score
        FROM cluster_organisation c1_
            WHERE MATCH (c1_.name) AGAINST ('test' IN BOOLEAN MODE) > 0

        ORDER BY `score` DESC
EOP;

        $em = $this->_em;
        $connection = $em->getConnection();
        $query = $connection->prepare($SQL);


        // works give result as array
        $result = $query->execute();

        // result is of type Doctrine\DBAL\Result
        // var_dump($result);

        echo "\n Result: \n";
        var_dump($result->fetchAll());

        die('searchTest2');
    }


    // try to combine 2 querybuilder into 1
    // idea behind this each querybuilder could be generated in its repository class
    // and combined outside of the entity repository
    public function searchTest3(
        Funder $funder,
        string $queryParam,
        int $limit
    ) {

        // same querybuilder from searchTest1
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->addSelect('cluster_entity_project.slug slug, cluster_entity_project.name name, cluster_entity_project.title title, cluster_entity_project.description description');
        $queryBuilder->addSelect("'project' AS type");
        $queryBuilder->addSelect("MATCH_AGAINST (cluster_entity_project.number, cluster_entity_project.name, cluster_entity_project.title, cluster_entity_project.description) AGAINST (:match IN BOOLEAN MODE) AS score");
        $queryBuilder->from(Project::class, 'cluster_entity_project');
        $queryBuilder->andWhere('MATCH_AGAINST (cluster_entity_project.number, cluster_entity_project.name, cluster_entity_project.title, cluster_entity_project.description) AGAINST (:match IN BOOLEAN MODE) > 0');
        $queryBuilder->setParameter('match', $queryParam);

        $queryBuilder2 = $this->_em->createQueryBuilder();
        $queryBuilder2->addSelect('cluster_entity_organisation.slug slug, cluster_entity_organisation.name name, cluster_entity_organisation.name title');
        $queryBuilder2->addSelect('NULLIF(1, 1) AS description'); // works description is then null
        $queryBuilder2->addSelect("'organisation' AS type");
        $queryBuilder2->addSelect("MATCH_AGAINST (cluster_entity_organisation.name) AGAINST (:match IN BOOLEAN MODE) AS score");
        $queryBuilder2->from(Organisation::class, 'cluster_entity_organisation');
        $queryBuilder2->andWhere('MATCH_AGAINST (cluster_entity_organisation.name) AGAINST (:match IN BOOLEAN MODE) > 0');
        $queryBuilder2->setParameter('match', $queryParam);

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();

        // needed without that i only get an empty array.
        // add test columns to the result mapping
        // $rsm->addScalarResult('id', 'id');
        // $rsm->addScalarResult('name', 'name');
        // $rsm->addScalarResult('description', 'description');

        // real columns
        $rsm->addScalarResult('slug', 'slug');
        $rsm->addScalarResult('name', 'name');
        $rsm->addScalarResult('title', 'title');
        $rsm->addScalarResult('description', 'description');
        $rsm->addScalarResult('type', 'type');
        $rsm->addScalarResult('score', 'score');

        $SQL = $queryBuilder->getQuery()->getSQL()
            . " UNION "
            . $queryBuilder2->getQuery()->getSQL()
            . " ORDER BY `score` DESC ";

        $SQL = $queryBuilder->getQuery()->getSQL();

        // :match replaced by ? in generated SQL
        // echo "\n Generated SQl: \n";
        // var_dump($SQL);

        // SELECT c0_.slug AS slug_0, c0_.name AS name_1, c0_.title AS title_2, c0_.description AS description_3, 'project' AS sclr_4, MATCH (c0_.number, c0_.name, c0_.title, c0_.description) AGAINST (? IN BOOLEAN MODE) AS sclr_5 FROM cluster_project c0_ WHERE MATCH (c0_.number, c0_.name, c0_.title, c0_.description) AGAINST (? IN BOOLEAN MODE) > 0 UNION SELECT c0_.slug AS slug_0, c0_.name AS name_1, c0_.name AS name_2, NULLIF(1, 1) AS sclr_3, 'organisation' AS sclr_4, MATCH (c0_.name) AGAINST (? IN BOOLEAN MODE) AS sclr_5 FROM cluster_organisation c0_ WHERE MATCH (c0_.name) AGAINST (? IN BOOLEAN MODE) > 0 ORDER BY `score` DESC

        // Sql has 4x ?
        // but i couldn't set the parameters? see below

        // test for an easier select to check if the parameter setting works
        // $SQL = 'SELECT id, name, description FROM cluster_project WHERE name = ?';
        // test with 2 parameters
        // $SQL = 'SELECT id, name, description FROM cluster_project WHERE name = ? and description = ?';

        $em = $this->_em;
        $query = $em->createNativeQuery($SQL, $rsm);

        // Error detail  "Positional parameter at index 0 does not have a bound value."
        // $query->setParameter('match', $queryParam);


        // https://phpdox.net/demo/Symfony2/classes/Doctrine_ORM_AbstractQuery/setParameter.xhtml
        // public function setParameter(string|integer $key, [mixed $value = null, [string $type = null]] )
        // $key — object
        //     The parameter position or name.
        // $value — mixed
        //     The parameter value

        // according to the example here it should be 1 https://www.doctrine-project.org/projects/doctrine-orm/en/2.9/reference/native-sql.html#the-nativequery-class

        // parameter has to be added by position number starting with 1?
        // $query->setParameter(1 , $queryParam);
        // $query->setParameter(2 , $queryParam);
        // $query->setParameter(3 , $queryParam);
        // $query->setParameter(4 , $queryParam);

        // i always get Invalid parameter number: number of bound variables does not match number of tokens...?

        // test to dynamically set them
        // echo "\n queryBuilder Parameters: \n";
        // var_dump($queryBuilder->getParameters());

        // echo "\n queryBuilder Parameters (size): \n";
        // var_dump(sizeof($queryBuilder->getParameters()));

        //  queryBuilder Parameters (size): int(1) => so i can't add it dynamically
        // foreach ($queryBuilder->getParameters() as $k => $p) {
        //     // $query->setParameter($k, $p->getValue(), $p->getType());
        //        echo "\n queryBuilder Parameter (name): \n";    // match
        //        var_dump($p->getName())."\n";
        //        echo "\n queryBuilder Parameter (value): \n";   // test = $queryParam value
        //        var_dump($p->getValue())."\n";
        //        echo "\n queryBuilder Parameter (type): \n";
        //        var_dump($p->getType())."\n";
        //        $query->setParameter($p->getName(), $p->getValue(), $p->getType());
        // }

        // setParameter for easier test SQL to check the result
        // $query->setParameter(0, 'AQUA');  //  first ?
        // $query->setParameter(1, 'AQUA2');  // second ?
        // strange that in the example linked above they use 1 for only one questionmark in the sql query...?


        // ok in the end i count the ? and add that number of parameters to it starting with 0
        $countOfQuestionMarks = substr_count($query->getSQL(), '?');
        // echo "\n countOfQuestionMarks: \n";
        // var_dump($countOfQuestionMarks);
        for ($i = 0; $i<$countOfQuestionMarks; $i++) {
            echo "\n setParameter $i to $queryParam: \n";
            $query->setParameter($i, $queryParam);
        }
        // => numbers of parameters is now correct.

        // echo "\n getParameters: \n";
        // var_dump($query->getParameters());

        // of type Doctrine\ORM\NativeQuery
        // var_dump($query);

        echo "\n getSql: \n";
        var_dump($query->getSQL());

        echo "\n getArrayResult: \n";
        var_dump($query->getArrayResult());

        // echo "\n getResult + HYDRATE_ARRAY: \n";
        // $result = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        // var_dump($result);

        //////////////////////////////////////////////
        // result always empty even with test SQL?? //
        //////////////////////////////////////////////
        ///... i need this lines...
        // $rsm->addScalarResult('id', 'id');
        // $rsm->addScalarResult('name', 'name');
        // $rsm->addScalarResult('description', 'description');

        /* dynamic union of two querybuildes doesn't work like that
        because the names in the generated sql aren't the names used in the sql query.
        e.g.
             ORDER BY `score` DESC wouldn't been found because its named "sclr_5" instead
             sclr_3 isn't named like the description column description_3

             with that i couldn't use the querybuilder for this native query.

        SELECT
            c0_.slug AS slug_0,
            c0_.name AS name_1,
            c0_.title AS title_2,
            c0_.description AS description_3,
            'project' AS sclr_4,
            MATCH (c0_.number, c0_.name, c0_.title, c0_.description) AGAINST ('test' IN BOOLEAN MODE) AS sclr_5
        FROM cluster_project c0_
            WHERE MATCH (c0_.number, c0_.name, c0_.title, c0_.description) AGAINST ('test' IN BOOLEAN MODE) > 0
        UNION

        SELECT
            c0_.slug AS slug_0,
            c0_.name AS name_1,
            c0_.name AS name_2,
            NULLIF(1, 1) AS sclr_3,
            'organisation' AS sclr_4,
            MATCH (c0_.name) AGAINST ('test' IN BOOLEAN MODE) AS sclr_5
        FROM cluster_organisation c0_
            WHERE MATCH (c0_.name) AGAINST ('test' IN BOOLEAN MODE) > 0
        ORDER BY `score` DESC
        */

        // => it would only work if the only one query is generated with an queryBuilder
        // and then still the column names which are needed for the $rsm wouldn't been correct.
        // and therefore the array always empty
        //
        // there is only this which doesn't help if we have multiple entities
        // $rsm->addEntityResult('App\Entity\AbstractUser', 'br');
        // $rsm->addFieldResult('br', 'user_id', 'id');
        //
        // or https://www.doctrine-project.org/projects/doctrine-orm/en/2.9/reference/native-sql.html#resultsetmappingbuilder
        //
        // $selectClause = $rsm->generateSelectClause(array(
        //     'u' => 't1',
        //     'g' => 't2'
        // ));
        // $sql = "SELECT " . $selectClause . " FROM users t1 JOIN groups t2 ON t1.group_id = t2.id";




        die('searchTest3');
    }


    public function searchTest4(
        Funder $funder,
        string $queryParam,
        int $limit
    ) {

        // https://github.com/doctrine/orm/issues/5657
        // union in queryBuilder is not supported.

        die('searchTest4');
    }

    public function searchTest(
        Funder $funder,
        string $query,
        int $limit
    ) {
    // ): QueryBuilder {


        // warning funderFilter not used for this tests!

        // test1
        // using querybuilder to select individualy and get an array result
        // it would also work if:
        // - do the queries in each entity repository
        // - combine the array results of each
        // - sort the array after highest "score"
        // - return the paginated array

        $this->searchTest1($funder, $query, $limit);


        // test2:
        // one manual sql query which does the union and returns already the sorted array result

        // $this->searchTest2($funder, $query, $limit);


        // test3:
        // try to combine 2 querybuilder into one query

        // $this->searchTest3($funder, $query, $limit);
        // doesn't work see comments at the end of the function.


        // test4:
        // create one querybuilder for the sql
        // $this->searchTest4($funder, $query, $limit);
        //
        // https://github.com/doctrine/orm/issues/5657
        // union in queryBuilder is not supported. so not an option

        // @ johan
        // so i guess the best is either searchTest1 or searchTest2
    }


    public function searchProjects(
        Funder $funder,
        string $query,
        int $limit
    ): QueryBuilder {

        $config = $this->_em->getConfiguration();
        $config->addCustomStringFunction('match_against', MatchAgainst::class);

        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('cluster_entity_project');
        $queryBuilder->from(Project::class, 'cluster_entity_project');

        // $queryBuilder->andWhere(
        //     $queryBuilder->expr()->orX(
        //         $queryBuilder->expr()->like('cluster_entity_project.number', ':like'),
        //         $queryBuilder->expr()->like('cluster_entity_project.name', ':like'),
        //         $queryBuilder->expr()->like('cluster_entity_project.title', ':like'),
        //         $queryBuilder->expr()->like('cluster_entity_project.description', ':like'),
        //     )
        // );
        // $queryBuilder->setParameter('like', sprintf('%%%s%%', $query));



        $queryBuilder->addSelect('MATCH_AGAINST (cluster_entity_project.number, cluster_entity_project.name, cluster_entity_project.title, cluster_entity_project.description) AGAINST (:match IN BOOLEAN MODE) as score');
        $queryBuilder->andWhere('MATCH_AGAINST (cluster_entity_project.number, cluster_entity_project.name, cluster_entity_project.title, cluster_entity_project.description) AGAINST (:match IN BOOLEAN MODE) > 0');
        $queryBuilder->setParameter('match', $query);

        $this->applyFunderFilter($queryBuilder, $funder);
        $queryBuilder->setMaxResults($limit);

        return $queryBuilder;
    }

    public function getProjectByFunderAndSlug(Funder $funder, string $slug): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('cluster_entity_project');
        $queryBuilder->from(Project::class, 'cluster_entity_project');
        $this->applyFunderFilter($queryBuilder, $funder);
        $queryBuilder->andWhere('cluster_entity_project.slug = :slug')
            ->setParameter('slug', $slug);
        return $queryBuilder;
    }

    public function fetchOrganisationTypes(Funder $funder, $filter): array
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

        $this->applyFunderFilter($queryBuilder, $funder);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchCountries(Funder $funder, $filter): array
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

        $this->applyFunderFilter($queryBuilder, $funder);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchProgrammeCalls(Funder $funder, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_project.programmeCall',
            $queryBuilder->expr()->count('cluster_entity_project.id')
        );

        $queryBuilder->from(Project::class, 'cluster_entity_project')
            ->groupBy('cluster_entity_project.programmeCall');

        $this->applyFunderFilter($queryBuilder, $funder);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchClusters(Funder $funder, $filter): array
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
//        $this->applyFunderFilter($queryBuilder, $funder);

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
//        $this->applyFunderFilter($queryBuilder, $funder);
        $secondaryClusters = $queryBuilder->getQuery()->getArrayResult();

        return array_map(static fn(array $cluster1, $cluster2) => [
            'name' => $cluster1['name'],
            '1'    => $cluster1[1],
            '2'    => $cluster2[1],
        ], $primaryClusters, $secondaryClusters);
    }

    public function fetchProjectStatuses(Funder $funder, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_project_status.status',
            $queryBuilder->expr()->count('cluster_entity_project.id')
        );
        $queryBuilder->from(Status::class, 'cluster_entity_project_status')
            ->join('cluster_entity_project_status.projects', 'cluster_entity_project')
            ->groupBy('cluster_entity_project_status');

        $this->applyFunderFilter($queryBuilder, $funder);

        return $queryBuilder->getQuery()->getArrayResult();
    }
}
