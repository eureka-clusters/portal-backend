<?php

declare(strict_types=1);

namespace Api\V1\Rest\ListResource;

use Admin\Service\UserService;
use Api\Paginator\DoctrineORMAdapter;
use Cluster\Entity\Organisation;
use Cluster\Entity\Project;
use Cluster\Provider\Project\PartnerProvider;
use Cluster\Service\OrganisationService;
use Cluster\Service\Project\PartnerService;
use Cluster\Service\ProjectService;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\Paginator\Adapter\ArrayAdapter;
use Laminas\Paginator\Paginator;

final class PartnerListener extends AbstractResourceListener
{
    public function __construct(
        private readonly PartnerService $partnerService,
        private readonly ProjectService $projectService,
        private readonly OrganisationService $organisationService,
        private readonly UserService $userService,
        private readonly PartnerProvider $partnerProvider
    ) {
    }

    public function fetchAll($params = []): Paginator
    {
        $user = $this->userService->findUserById((int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']);

        if (null === $user || !$user->isFunder()) {
            return new Paginator(new ArrayAdapter());
        }

        switch (true) {
            case isset($params->project):
                /** @var Project $project */
                $project = $this->projectService->findProjectBySlug($params->project);

                if (null === $project) {
                    return new Paginator(new ArrayAdapter());
                }

                $partnerQueryBuilder = $this->partnerService->getPartnersByProject($project);
                break;
            case isset($params->organisation):
                /** @var Organisation $organisation */
                $organisation = $this->organisationService->findOrganisationBySlug($params->organisation);

                if (null === $organisation) {
                    return new Paginator(new ArrayAdapter());
                }

                $partnerQueryBuilder = $this->partnerService->getPartnersByOrganisation($organisation);
                break;
            default:
                $partnerQueryBuilder = $this->partnerService->getPartners($user->getFunder(), []);
        }

        $doctrineORMAdapter = new DoctrineORMAdapter($partnerQueryBuilder);
        $doctrineORMAdapter->setProvider($this->partnerProvider);

        return new Paginator($doctrineORMAdapter);
    }
}
