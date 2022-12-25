<?php

declare(strict_types=1);

namespace Api\V1\Rest\ListResource;

use Admin\Service\UserService;
use Api\Paginator\DoctrineORMAdapter;
use Cluster\Entity\Organisation;
use Cluster\Entity\Project;
use Cluster\Provider\Project\PartnerProvider;
use Cluster\Provider\Project\PartnerYearProvider;
use Cluster\Service\OrganisationService;
use Cluster\Service\Project\PartnerService;
use Cluster\Service\ProjectService;
use Doctrine\Common\Collections\Criteria;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\Json\Json;
use Laminas\Paginator\Adapter\ArrayAdapter;
use Laminas\Paginator\Paginator;

final class PartnerListener extends AbstractResourceListener
{
    public function __construct(
        private readonly PartnerService $partnerService,
        private readonly ProjectService $projectService,
        private readonly OrganisationService $organisationService,
        private readonly UserService $userService,
        private readonly PartnerProvider $partnerProvider,
        private readonly PartnerYearProvider $partnerYearProvider,
    ) {
    }

    public function fetchAll($params = []): Paginator
    {
        $user = $this->userService->findUserById(
            id: (int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']
        );

        if (null === $user) {
            return new Paginator(adapter: new ArrayAdapter());
        }

        $hasYears = false;

        switch (true) {
            case isset($params->project):
                /** @var Project $project */
                $project = $this->projectService->findProjectBySlug(slug: $params->project);

                if (null === $project) {
                    return new Paginator(adapter: new ArrayAdapter());
                }

                $partnerQueryBuilder = $this->partnerService->getPartnersByProject(project: $project);
                break;
            case isset($params->organisation):
                /** @var Organisation $organisation */
                $organisation = $this->organisationService->findOrganisationBySlug(slug: $params->organisation);

                if (null === $organisation) {
                    return new Paginator(adapter: new ArrayAdapter());
                }

                $partnerQueryBuilder = $this->partnerService->getPartnersByOrganisation(organisation: $organisation);
                break;
            default:

                $encodedFilter = (string)$this->getEvent()->getQueryParams()?->get(name: 'filter');

                //The filter is a base64 encoded serialised json string
                $filter      = base64_decode(string: $encodedFilter, strict: true);
                $arrayFilter = Json::decode(encodedValue: $filter, objectDecodeType: Json::TYPE_ARRAY);

                $defaultSort = 'partner.organisation.name';
                $sort        = $this->getEvent()->getQueryParams()?->get(name: 'sort', default: $defaultSort);
                $order       = $this->getEvent()->getQueryParams()?->get(
                    name: 'order',
                    default: strtolower(
                        string: Criteria::ASC
                    )
                );

                $hasYears = !empty($arrayFilter['year']);

                $partnerQueryBuilder = $this->partnerService->getPartners(
                    user: $user,
                    filter: $arrayFilter,
                    sort: $sort,
                    order: $order
                );
        }

        $doctrineORMAdapter = new DoctrineORMAdapter(query: $partnerQueryBuilder);
        $doctrineORMAdapter->setProvider(provider: $hasYears ? $this->partnerYearProvider : $this->partnerProvider);

        return new Paginator(adapter: $doctrineORMAdapter);
    }
}
