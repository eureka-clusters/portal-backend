<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Service;

use Application\Service\AbstractService;
use Cluster\Entity;
use Cluster\Entity\Funder;
use Cluster\Entity\Project;
use Doctrine\ORM\EntityManager;

/**
 *
 */
class ProjectService extends AbstractService
{
    private ClusterService $clusterService;

    public function __construct(EntityManager $entityManager, ClusterService $clusterService)
    {
        parent::__construct($entityManager);

        $this->clusterService = $clusterService;
    }

    public function getProjects(Funder $funder, array $filter): array
    {
        return $this->entityManager->getRepository(Project::class)->getProjectsByFunderAndFilter($funder, $filter);
    }

    public function generateFacets(Funder $funder, array $filter): array
    {
        $countries         = $this->entityManager->getRepository(Project::class)->fetchCountries($funder, $filter);
        $organisationTypes = $this->entityManager->getRepository(Project::class)->fetchOrganisationTypes(
            $funder,
            $filter
        );
        $primaryClusters   = $this->entityManager->getRepository(Project::class)->fetchPrimaryClusters(
            $funder,
            $filter
        );
        $projectStatuses   = $this->entityManager->getRepository(Project::class)->fetchProjectStatuses(
            $funder,
            $filter,
        );

        $countriesIndexed = array_map(static function (array $country) {
            return [
                'country' => $country['country'],
                'amount'  => $country[1]
            ];
        }, $countries);

        $organisationTypesIndexed = array_map(static function (array $organisationType) {
            return [
                'organisationType' => $organisationType['type'],
                'amount'           => $organisationType[1]
            ];
        }, $organisationTypes);

        $primaryClustersIndexed = array_map(static function (array $primaryCluster) {
            return [
                'primaryCluster' => $primaryCluster['name'],
                'amount'         => $primaryCluster[1]
            ];
        }, $primaryClusters);

        $projectStatusIndexed = array_map(static function (array $projectStatus) {
            return [
                'projectStatus' => $projectStatus['status'],
                'amount'        => $projectStatus[1]
            ];
        }, $projectStatuses);

        return [
            'countries'          => $countriesIndexed,
            'organisation_types' => $organisationTypesIndexed,
            'project_status'     => $projectStatusIndexed,
            'primary_clusters'   => $primaryClustersIndexed,
        ];
    }

    public function findOrCreateProject(\stdClass $data): Entity\Project
    {
        $project = $this->findProjectByIdentifier($data->internal_identifier);

        //If we cannot find the project we create a new one. Only set the identifier as we will later overwrite/update the properties
        if (null === $project) {
            $project = new Entity\Project();
            $project->setIdentifier($data->internal_identifier);
        }

        $project->setNumber($data->project_number);
        $project->setName($data->project_name);
        $project->setTitle($data->project_title);
        $project->setDescription($data->project_description);
        $project->setProgramme($data->programme);
        $project->setProgrammeCall($data->programme_call);

        $project->setProjectLeader($data->project_leader);
        $project->setTechnicalArea($data->technical_area);

        //Find or create the primary cluster
        $primaryCluster = $this->clusterService->findOrCreateCluster($data->primary_cluster);

        // $primaryCluster = $this->clusterService->findClusterByName($data->primary_cluster);
        // if (null === $primaryCluster) {
        //     throw new \InvalidArgumentException(
        //         sprintf('The primary cluster %s cannot be found', $data->primary_cluster)
        //     );
        // }

        $project->setPrimaryCluster($primaryCluster);

        // @Johan what happens if the secondary cluster can't be found?
        // If found, set the secondary cluster
        // $secondaryCluster = $this->clusterService->findClusterByName((string)$data->secondary_cluster);
        // if (null !== $secondaryCluster) {
        //     $project->setSecondaryCluster($secondaryCluster);
        // }

        // my suggestion
        if (null !== $data->secondary_cluster) {
            $secondaryCluster = $this->clusterService->findOrCreateCluster($data->secondary_cluster);
            $project->setSecondaryCluster($secondaryCluster);
        }

        //Find the status
        $status = $this->entityManager->getRepository(Entity\Project\Status::class)->findOneBy(
            ['status' => $data->project_status]
        );

        //If we cannot find the status, we create a new one
        if (null === $status) {
            $status = new Entity\Project\Status();
            $status->setStatus($data->project_status);
        }

        $project->setStatus($status);

        //Handle the dates
        if ($data->official_start_date) {
            $officialStartDate = \DateTime::createFromFormat(\DateTimeInterface::ATOM, $data->official_start_date);
            $project->setOfficialStartDate($officialStartDate ?: null);
        }

        if ($data->official_end_date) {
            $officialEndDate = \DateTime::createFromFormat(\DateTimeInterface::ATOM, $data->official_end_date);
            $project->setOfficialEndDate($officialEndDate ?: null);
        }

        if ($data->label_date) {
            $labelDate = \DateTime::createFromFormat(\DateTimeInterface::ATOM, $data->label_date);
            $project->setLabelDate($labelDate ?: null);
        }

        if ($data->cancel_date) {
            $cancelDate = \DateTime::createFromFormat(\DateTimeInterface::ATOM, (string)$data->cancel_date);
            $project->setCancelDate($cancelDate ?: null);
        }

        $this->save($project);
        return $project;
    }

    public function findProjectByIdentifier(string $identifier): ?Entity\Project
    {
        return $this->entityManager->getRepository(Entity\Project::class)->findOneBy(
            ['identifier' => $identifier]
        );
    }
}
