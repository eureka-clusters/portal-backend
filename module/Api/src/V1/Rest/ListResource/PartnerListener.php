<?php

/**
 * Jield BV all rights reserved
 *
 * @author      Dr. ir. Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2020 Jield BV (https://jield.nl)
 */

namespace Api\V1\Rest\ListResource;

use Admin\Service\UserService;
use Cluster\Entity\Organisation;
use Cluster\Entity\Project;
use Cluster\Provider\Project\PartnerProvider;
use Cluster\Rest\Collection\PartnerCollection;
use Cluster\Service\OrganisationService;
use Cluster\Service\Project\PartnerService;
use Cluster\Service\ProjectService;
use Laminas\ApiTools\Rest\AbstractResourceListener;

/**
 * Class ProjectListener
 * @package Api\V1\Rest\ListResource
 */
final class PartnerListener extends AbstractResourceListener
{
    private PartnerService      $partnerService;
    private ProjectService      $projectService;
    private OrganisationService $organisationService;
    private UserService         $userService;
    private PartnerProvider     $partnerProvider;

    public function __construct(
        PartnerService $partnerService,
        ProjectService $projectService,
        OrganisationService $organisationService,
        UserService $userService,
        PartnerProvider $partnerProvider
    ) {
        $this->partnerService      = $partnerService;
        $this->projectService      = $projectService;
        $this->organisationService = $organisationService;
        $this->userService         = $userService;
        $this->partnerProvider     = $partnerProvider;
    }

    public function fetchAll($params = [])
    {
        $user = $this->userService->findUserById((int)$this->getIdentity()->getAuthenticationIdentity()['user_id']);

        if (null === $user || !$user->isFunder()) {
            return [];
        }

        switch (true) {
            case isset($params->project):
                /** @var Project $project */
                $project = $this->projectService->findProjectBySlug($params->project);

                if (null === $project) {
                    return [];
                }

                $partnerQueryBuilder = $this->partnerService->getPartnersByProject($project);
                break;
            case isset($params->organisation):
                /** @var Organisation $organisation */
                $organisation = $this->organisationService->findOrganisationBySlug($params->organisation);

                if (null === $organisation) {
                    return [];
                }

                $partnerQueryBuilder = $this->partnerService->getPartnersByOrganisation($organisation);
                break;
            default:
                $partnerQueryBuilder = $this->partnerService->getPartners($user->getFunder(), []);
        }

        return (new PartnerCollection($partnerQueryBuilder, $this->partnerProvider))->getItems(
            $params->offset,
            $params->amount ?? 100
        );
    }
}
