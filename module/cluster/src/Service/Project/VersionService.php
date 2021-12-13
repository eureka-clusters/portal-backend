<?php

declare(strict_types=1);

namespace Cluster\Service\Project;

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
    public function findVersionTypeById(int $id): Entity\Version\Type
    {
        return $this->entityManager->find(Entity\Version\Type::class, $id);
    }

    public function createVersionFromData(
        stdClass $data,
        Entity\Version\Type $type,
        Entity\Project $project
    ): Entity\Project\Version {
        $version = new Entity\Project\Version();
        $version->setProject($project);

        $version->setType($type);

        //Find the status
        $status = $this->findOrCreateVersionStatus($data->status);

        $version->setStatus($status);

        //Handle the submission date
        $submissionDate = DateTime::createFromFormat(DateTimeInterface::ATOM, $data->submission_date);
        $version->setSubmissionDate($submissionDate);

        $version->setCosts($data->totalCosts);
        $version->setEffort($data->totalEffort);

        //@todo: We keep an array here, might need to create entities
        $version->setCountries($data->countries);

        $this->save($version);

        return $version;
    }

    public function findOrCreateVersionStatus(string $statusName): Entity\Version\Status
    {
        $status = $this->entityManager->getRepository(Entity\Version\Status::class)
            ->findOneBy(['status' => $statusName]);

        if (null === $status) {
            $status = new Entity\Version\Status();
            $status->setStatus($statusName);
            $this->save($status);
        }

        return $status;
    }

    public function findVersionType(string $typeName): Entity\Version\Type
    {
        $type = $this->entityManager->getRepository(Entity\Version\Type::class)
            ->findOneBy(['type' => $typeName]);

        if (null === $type) {
            throw new InvalidArgumentException(sprintf("Project version type \"%s\" cannot be found", $typeName));
        }

        return $type;
    }

    public function parseTotalCostsByProjectVersion(Entity\Project\Version $projectVersion): float
    {
        /** @var CostsAndEffort $repository */
        $repository = $this->entityManager->getRepository(Entity\Project\Version\CostsAndEffort::class);

        return $repository->parseTotalCostsByProjectVersion($projectVersion);
    }

    public function parseTotalEffortByProjectVersion(Entity\Project\Version $projectVersion): float
    {
        /** @var CostsAndEffort $repository */
        $repository = $this->entityManager->getRepository(Entity\Project\Version\CostsAndEffort::class);

        return $repository->parseTotalEffortByProjectVersion($projectVersion);
    }
}
