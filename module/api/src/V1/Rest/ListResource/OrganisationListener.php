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
        $sort        = $this->getEvent()->getQueryParams()?->get(name: 'sort', default: $defaultSort);
        $order       = $this->getEvent()->getQueryParams()?->get(name: 'order', default: 'asc');

        $partnerQueryBuilder = $this->organisationService->getOrganisations(filter: [], sort: $sort, order: $order);

        $doctrineORMAdapter = new DoctrineORMAdapter(query: $partnerQueryBuilder);
        $doctrineORMAdapter->setProvider(provider: $this->organisationProvider);

        return new Paginator(adapter: $doctrineORMAdapter);
    }
}
