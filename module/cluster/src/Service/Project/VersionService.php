<?php

declare(strict_types=1);

namespace Cluster\Service\Project;

use Application\Service\AbstractService;
use Cluster\Entity;
use Cluster\Entity\Project;
use Cluster\Entity\Project\Version;
use Cluster\Entity\Version\Status;
use Cluster\Entity\Version\Type;
use Cluster\Repository\Project\Version\CostsAndEffort;
use DateTime;
use DateTimeInterface;
use InvalidArgumentException;
use stdClass;

use function sprintf;

class VersionService extends AbstractService
{
    public function createVersionFromData(
        stdClass $data,
        Type $type,
        Project $project
    ): Version {
        $version = new Version();
        $version->setProject(project: $project);

        $version->setType(type: $type);

        //Find the status
        $status = $this->findOrCreateVersionStatus(statusName: $data->status);

        $version->setStatus(status: $status);

        //Handle the submission date
        $submissionDate = DateTime::createFromFormat(format: DateTimeInterface::ATOM, datetime: $data->submissionDate);
        $version->setSubmissionDate(submissionDate: $submissionDate);

        $version->setCosts(costs: $data->totalCosts);
        $version->setEffort(effort: $data->totalEffort);

        //@todo: We keep an array here, might need to create entities
        $version->setCountries(countries: $data->countries);

        $this->save(entity: $version);

        return $version;
    }

    public function findOrCreateVersionStatus(string $statusName): Status
    {
        $status = $this->entityManager->getRepository(entityName: Status::class)
            ->findOneBy(criteria: ['status' => $statusName]);

        if (null === $status) {
            $status = new Status();
            $status->setStatus(status: $statusName);
            $this->save(entity: $status);
        }

        return $status;
    }

    public function findVersionType(string $typeName): Type
    {
        $type = $this->entityManager->getRepository(entityName: Type::class)
            ->findOneBy(criteria: ['type' => $typeName]);

        if (null === $type) {
            throw new InvalidArgumentException(message: sprintf("Project version type \"%s\" cannot be found", $typeName));
        }

        return $type;
    }

    public function parseTotalCostsByProjectVersion(Version $projectVersion): float
    {
        /** @var CostsAndEffort $repository */
        $repository = $this->entityManager->getRepository(entityName: Entity\Project\Version\CostsAndEffort::class);

        return $repository->parseTotalCostsByProjectVersion(projectVersion: $projectVersion);
    }

    public function parseTotalEffortByProjectVersion(Version $projectVersion): float
    {
        /** @var CostsAndEffort $repository */
        $repository = $this->entityManager->getRepository(entityName: Entity\Project\Version\CostsAndEffort::class);

        return $repository->parseTotalEffortByProjectVersion(projectVersion: $projectVersion);
    }
}
