<?php

declare(strict_types=1);

namespace Api\V1\Rest\ListResource;

use Api\Paginator\DoctrineORMAdapter;
use Cluster\Provider\OrganisationProvider;
use Cluster\Service\OrganisationService;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\Paginator\Paginator;

final class OrganisationListener extends AbstractResourceListener
{
    public function __construct(
        private OrganisationService $organisationService,
        private OrganisationProvider $organisationProvider
    ) {
    }

    public function fetchAll($params = []): Paginator
    {
        $partnerQueryBuilder = $this->organisationService->getOrganisations([]);

        $doctrineORMAdapter = new DoctrineORMAdapter($partnerQueryBuilder);
        $doctrineORMAdapter->setProvider($this->organisationProvider);

        return new Paginator($doctrineORMAdapter);
    }
}
