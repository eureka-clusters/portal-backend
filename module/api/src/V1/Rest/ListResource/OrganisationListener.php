<?php

declare(strict_types=1);

namespace Api\V1\Rest\ListResource;

use Cluster\Provider\OrganisationProvider;
use Cluster\Rest\Collection\OrganisationCollection;
use Cluster\Service\OrganisationService;
use Laminas\ApiTools\Rest\AbstractResourceListener;

final class OrganisationListener extends AbstractResourceListener
{
    public function __construct(
        private OrganisationService $organisationService,
        private OrganisationProvider $organisationProvider
    ) {
    }

    public function fetchAll($params = [])
    {
        //var_dump($this->getIdentity()?->getName());
        //$user = $this->userService->findUserById((int) $this->getIdentity()?->getName());

        $partnerQueryBuilder = $this->organisationService->getOrganisations([]);

        return (new OrganisationCollection($partnerQueryBuilder, $this->organisationProvider))->getItems(
            $params->offset,
            $params->amount ?? 25
        );
    }
}
