<?php

declare(strict_types=1);

namespace Api\Service;

use Api\Entity\OAuth\Client;
use Application\Service\AbstractService;
use Doctrine\Common\Collections\Criteria;
use RuntimeException;

class OAuthService extends AbstractService
{
    public function findClientByClientId(string $clientId): Client
    {
        $repository = $this->entityManager->getRepository(Client::class);
        $client     = $repository->findOneBy(['clientId' => $clientId]);

        if (null === $client) {
            throw new RuntimeException("No JWT client available");
        }

        return $client;
    }

    public function findLatestClient(): Client
    {
        $repository = $this->entityManager->getRepository(Client::class);
        $clients    = $repository->findBy([], ['clientId' => Criteria::ASC], 1);

        if (empty($clients)) {
            throw new RuntimeException("No JWT client available");
        }

        return array_pop($clients);
    }
}
