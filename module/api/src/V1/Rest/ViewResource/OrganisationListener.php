<?php

declare(strict_types=1);

namespace Api\V1\Rest\ViewResource;

use Cluster\Provider\OrganisationProvider;
use Cluster\Service\OrganisationService;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\Rest\AbstractResourceListener;

final class OrganisationListener extends AbstractResourceListener
{
    public function __construct(
        private readonly OrganisationService $organisationService,
        private readonly OrganisationProvider $organisationProvider
    ) {
    }

    public function fetch($slug = null)
    {
        $organisation = $this->organisationService->findOrganisationBySlug($slug);

        if (null === $organisation) {
            return new ApiProblem(404, 'The selected organisation cannot be found');
        }

        return $this->organisationProvider->generateArray($organisation);
    }
}
