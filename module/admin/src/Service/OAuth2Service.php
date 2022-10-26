<?php

declare(strict_types=1);

namespace Admin\Service;

use Admin\Entity\User;
use Api\Entity\OAuth\Client;
use Application\Service\AbstractService;
use Doctrine\Common\Collections\Criteria;
use OAuth2\Encryption\Jwt;
use RuntimeException;

class OAuth2Service extends AbstractService
{
    public function findClientByClientId(string $clientId): Client
    {
        $repository = $this->entityManager->getRepository(entityName: Client::class);
        $client = $repository->findOneBy(criteria: ['clientId' => $clientId]);

        if (null === $client) {
            throw new RuntimeException(message: "No JWT client available");
        }

        return $client;
    }

    public function findLatestClient(): Client
    {
        $repository = $this->entityManager->getRepository(entityName: Client::class);
        $clients = $repository->findBy(criteria: [], orderBy: ['clientId' => Criteria::ASC], limit: 1);

        if (empty($clients)) {
            throw new RuntimeException(message: "No JWT client available");
        }

        return array_pop(array: $clients);
    }

    public function generateJwtToken(Client $client, User $user): string
    {
        $payload = [
            'id' => 1, // for BC (see #591)
            'jti' => 1,
            'iss' => 'eureka-clusters',
            'aud' => $client->getClientId(),
            'sub' => $user->getId(),
            'exp' => (new \DateTime())->add(interval: new \DateInterval(duration: 'P1D'))->getTimestamp(),
            'iat' => time(),
            'token_type' => $client->getPublicKey()?->getEncryptionAlgorithm(),
            'scope' => 'openid'
        ];

        $jwtHelper = new Jwt();

        if ($client->getPublicKey()?->getEncryptionAlgorithm() === 'RS256') {
            return $jwtHelper->encode(
                payload: $payload,
                key: $client->getPublicKey()?->getPrivateKey(),
                algo: $client->getPublicKey()?->getEncryptionAlgorithm()
            );
        }

        return $jwtHelper->encode(
            payload: $payload,
            key: $client->getPublicKey()?->getPublicKey(),
            algo: 'HS256'
        );
    }
}
