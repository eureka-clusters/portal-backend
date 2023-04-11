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
use Jield\Search\ValueObject\SearchFormResult;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\Json\Json;
use Laminas\Paginator\Paginator;
use OpenApi\Attributes as OA;

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

    #[OA\Get(
        path: '/api/list/partner',
        description: 'List of project partners',
        summary: 'Get a list of partners project',
        tags: ['Project'],
        parameters: [
            new OA\Parameter(
                name: 'organisation',
                description: 'Organisation ID to filter on',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
            new OA\Parameter(
                name: 'project',
                description: 'Project ID to filter on',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
            new OA\Parameter(
                name: 'filter',
                description: 'Base64 encoded JSON filter',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: null
            ),
            new OA\Parameter(
                name: 'query',
                description: 'Search Query',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: null
            ),
            new OA\Parameter(
                name: 'order',
                description: 'Sort order',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'name'
            ),
            new OA\Parameter(
                name: 'direction',
                description: 'Sort direction',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'asc'
            ),
            new OA\Parameter(
                name: 'pageSize',
                description: 'Amount per page',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                example: 25
            ),
            new OA\Parameter(
                name: 'page',
                description: 'Page',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(ref: '#/components/responses/project_partner', response: 200),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 400, description: 'Project or partner not found'),
        ],
    )]
    public function fetchAll($params = []): Paginator|ApiProblem
    {
        $user = $this->userService->findUserById(
            id: (int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']
        );

        $filter = $params->toArray();

        //Inject the encoded filter from the results
        if (isset($params->filter)) {
            $filter           = base64_decode(string: $params->filter, strict: true);
            $filter['filter'] = Json::decode(encodedValue: $filter, objectDecodeType: Json::TYPE_ARRAY);
        }

        $searchFormResult = SearchFormResult::fromArray($filter);

        $hasYears = false;

        switch (true) {
            case isset($params->project):
                /** @var Project $project */
                $project = $this->projectService->findProjectBySlug(slug: $params->project);

                if (null === $project) {
                    return new ApiProblem(status: 400, detail: 'Project not found');
                }

                $partnerQueryBuilder = $this->partnerService->getPartnersByProject(
                    user: $user,
                    project: $project,
                    searchFormResult: $searchFormResult,
                );
                break;
            case isset($params->organisation):
                /** @var Organisation $organisation */
                $organisation = $this->organisationService->findOrganisationBySlug(slug: $params->organisation);

                if (null === $organisation) {
                    return new ApiProblem(status: 400, detail: 'Partner not found');
                }

                $partnerQueryBuilder = $this->partnerService->getPartnersByOrganisation(
                    user: $user,
                    organisation: $organisation,
                    searchFormResult: $searchFormResult,
                );
                break;
            default:

                $hasYears = !empty($filter['filter']['year']);

                $partnerQueryBuilder = $this->partnerService->getPartners(
                    user: $user,
                    searchFormResult: $searchFormResult,
                );
        }

        $doctrineORMAdapter = new DoctrineORMAdapter(query: $partnerQueryBuilder);
        $doctrineORMAdapter->setProvider(provider: $hasYears ? $this->partnerYearProvider : $this->partnerProvider);

        return new Paginator(adapter: $doctrineORMAdapter);
    }
}
