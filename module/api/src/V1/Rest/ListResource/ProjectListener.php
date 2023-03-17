<?php

declare(strict_types=1);

namespace Api\V1\Rest\ListResource;

use Admin\Service\UserService;
use Api\Paginator\DoctrineORMAdapter;
use Cluster\Provider\ProjectProvider;
use Cluster\Service\ProjectService;
use Doctrine\Common\Collections\Criteria;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\Json\Json;
use Laminas\Paginator\Adapter\ArrayAdapter;
use Laminas\Paginator\Paginator;

final class ProjectListener extends AbstractResourceListener
{
    public function __construct(
        private readonly ProjectService $projectService,
        private readonly UserService $userService,
        private readonly ProjectProvider $projectProvider
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

        $encodedFilter = $params->filter ?? null;
        $sort          = $params->sort ?? 'name';
        $order         = $params->order ?? strtolower(string: Criteria::DESC);

        $arrayFilter = [];
        if (!empty($encodedFilter)) {
            //The filter is a base64 encoded serialised json string
            $filter = base64_decode(string: $encodedFilter, strict: true);
            // $arrayFilter = json_decode($filter, true, 512, JSON_THROW_ON_ERROR);
            $arrayFilter = Json::decode(encodedValue: $filter, objectDecodeType: Json::TYPE_ARRAY);
        }

        $projectQueryBuilder = $this->projectService->getProjects(
            user: $user,
            filter: $arrayFilter,
            sort: $sort,
            order: $order
        );
        $doctrineORMAdapter  = new DoctrineORMAdapter(query: $projectQueryBuilder);
        $doctrineORMAdapter->setProvider(provider: $this->projectProvider);

        return new Paginator(adapter: $doctrineORMAdapter);
    }
}
