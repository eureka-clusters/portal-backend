<?php

declare(strict_types=1);

namespace Api\V1\Rest\StatisticsResource\Results;

use Admin\Service\UserService;
use Cluster\Provider\ProjectProvider;
use Cluster\Rest\Collection\ProjectCollection;
use Cluster\Service\ProjectService;
use Laminas\ApiTools\Rest\AbstractResourceListener;

use function base64_decode;
use function json_decode;

use const JSON_THROW_ON_ERROR;

final class ProjectListener extends AbstractResourceListener
{
    public function __construct(private ProjectService $projectService, private UserService $userService, private ProjectProvider $projectProvider)
    {
    }

    public function fetchAll($params = [])
    {
        $user = $this->userService->findUserById((int) $this->getIdentity()?->getName());

        if (null === $user || ! $user->isFunder()) {
            return [];
        }

        $encodedFilter = $this->getEvent()->getQueryParams()->get('filter');

        //The filter is a base64 encoded serialised json string
        $filter      = base64_decode($encodedFilter);
        $arrayFilter = json_decode($filter, true, 512, JSON_THROW_ON_ERROR);

        $projects = $this->projectService->getProjects($user->getFunder(), $arrayFilter);

        return (new ProjectCollection($projects, $this->projectProvider))->getItems(
            $params->offset,
            $params->amount ?? 100
        );
    }
}
