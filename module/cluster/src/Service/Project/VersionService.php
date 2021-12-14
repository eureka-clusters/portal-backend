<?php

declare(strict_types=1);

namespace Cluster\Service\Project;

use Cluster\Entity\Version\Type;
use Cluster\Entity\Project;
use Cluster\Entity\Project\Version;
use Cluster\Entity\Version\Status;
use Application\Service\AbstractService;
use Cluster\Entity;
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
        $version->setProject($project);

        $version->setType($type);

        //Find the status
        $status = $this->findOrCreateVersionStatus($data->status);

        $version->setStatus($status);

        //Handle the submission date
        $submissionDate = DateTime::createFromFormat(DateTimeInterface::ATOM, $data->submissionDate);
        $version->setSubmissionDate($submissionDate);

        $version->setCosts($data->totalCosts);
        $version->setEffort($data->totalEffort);

        //@todo: We keep an array here, might need to create entities
        $version->setCountries($data->countries);

        $this->save($version);

        return $version;
    }

    public function findOrCreateVersionStatus(string $statusName): Status
    {
        $status = $this->entityManager->getRepository(Status::class)
            ->findOneBy(['status' => $statusName]);

        if (null === $status) {
            $status = new Status();
            $status->setStatus($statusName);
            $this->save($status);
        }

        return $status;
    }

    public function findVersionType(string $typeName): Type
    {
        $type = $this->entityManager->getRepository(Type::class)
            ->findOneBy(['type' => $typeName]);

        if (null === $type) {
            throw new InvalidArgumentException(sprintf("Project version type \"%s\" cannot be found", $typeName));
        }

        return $type;
    }

    public function parseTotalCostsByProjectVersion(Version $projectVersion): float
    {
        /** @var CostsAndEffort $repository */
        $repository = $this->entityManager->getRepository(Entity\Project\Version\CostsAndEffort::class);

        return $repository->parseTotalCostsByProjectVersion($projectVersion);
    }

    public function parseTotalEffortByProjectVersion(Version $projectVersion): float
    {
        /** @var CostsAndEffort $repository */
        $repository = $this->entityManager->getRepository(Entity\Project\Version\CostsAndEffort::class);

        return $repository->parseTotalEffortByProjectVersion($projectVersion);
    }
}
