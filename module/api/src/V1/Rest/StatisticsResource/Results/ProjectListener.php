<?php

declare(strict_types=1);

namespace Api\V1\Rest\StatisticsResource\Results;

use Admin\Service\UserService;
use Api\Paginator\DoctrineORMAdapter;
use Cluster\Provider\ProjectProvider;
use Cluster\Service\ProjectService;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\Json\Json;
use Laminas\Paginator\Adapter\ArrayAdapter;
use Laminas\Paginator\Paginator;

use function base64_decode;

final class ProjectListener extends AbstractResourceListener
{
    public function __construct(
        private ProjectService $projectService,
        private UserService $userService,
        private ProjectProvider $projectProvider
    ) {
    }

    public function fetchAll($params = []): Paginator
    {
        $user = $this->userService->findUserById((int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']);

        if (null === $user || !$user->isFunder()) {
            return new Paginator(new ArrayAdapter());
        }

        $encodedFilter = $this->getEvent()->getQueryParams()->get('filter');

        //The filter is a base64 encoded serialised json string
        $filter = base64_decode($encodedFilter);
        // $arrayFilter = json_decode($filter, true, 512, JSON_THROW_ON_ERROR);
        $arrayFilter = Json::decode($filter, Json::TYPE_ARRAY);

        $defaultorder = 'asc';
        $defaultSort  = 'project.name';

        $sort  = $this->getEvent()->getQueryParams()?->get('sort', $defaultSort);
        $order = $this->getEvent()->getQueryParams()?->get('order', $defaultorder);

        $projectQueryBuilder = $this->projectService->getProjects($user->getFunder(), $arrayFilter, $sort, $order);
        $doctrineORMAdapter  = new DoctrineORMAdapter($projectQueryBuilder);
        $doctrineORMAdapter->setProvider($this->projectProvider);

        return new Paginator($doctrineORMAdapter);
    }
}
