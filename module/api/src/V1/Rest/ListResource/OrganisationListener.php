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
        private readonly OrganisationService $organisationService,
        private readonly OrganisationProvider $organisationProvider
    ) {
    }

    public function fetchAll($params = []): Paginator
    {
        $defaultSort = 'organisation.name';
        $sort = $this->getEvent()->getQueryParams()?->get('sort', $defaultSort);
        $order = $this->getEvent()->getQueryParams()?->get('order', 'asc');

        $partnerQueryBuilder = $this->organisationService->getOrganisations([], $sort, $order);

        $doctrineORMAdapter = new DoctrineORMAdapter($partnerQueryBuilder);
        $doctrineORMAdapter->setProvider($this->organisationProvider);

        return new Paginator($doctrineORMAdapter);
    }
}
