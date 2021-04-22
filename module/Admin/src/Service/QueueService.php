<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Admin\Service;

use Admin\Entity;
use Admin\Repository;
use Application\Service\AbstractService;
use SlmQueue\Worker\Event\ProcessJobEvent;

/**
 * Class QueueService
 * @package Admin\Service
 */
class QueueService extends AbstractService
{
    public function truncateQueue(): void
    {
        /** @var Repository\Queue $repository */
        $repository = $this->entityManager->getRepository(Entity\Queue::class);

        $repository->truncateQueue();
    }

    public function restartJobs(array $ids): void
    {
        foreach ($ids as $id) {
            $queue = $this->findQueueById($id);
            if (null !== $queue) {
                $queue->setStatus(ProcessJobEvent::JOB_STATUS_SUCCESS);
                $queue->setExecuted(null);
                $queue->setFinished(null);
                $this->entityManager->persist($queue);
            }
        }

        $this->entityManager->flush();
    }

    public function findQueueById(int $id): ?Entity\Queue
    {
        return $this->entityManager->getRepository(Entity\Queue::class)->find($id);
    }
}
