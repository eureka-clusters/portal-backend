<?php

declare(strict_types=1);

namespace Admin\Service;

use Admin\Entity\User;
use Api\Entity\OAuth\Client;
use Api\Entity\OAuth\Service;
use Application\Service\AbstractService;
use Doctrine\Common\Collections\Criteria;
use GuzzleHttp\RequestOptions;
use Laminas\Json\Json;
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

    public function findServiceById(int $id): ?Service
    {
        return $this->entityManager->find(Service::class, $id);
    }

    public function fetchAccessTokenFromService(Service $service)
    {
        $guzzle = new \GuzzleHttp\Client();

        $response = $guzzle->request(
            'POST',
            $service->getAccessTokenUrl(),
            [
                RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                RequestOptions::DEBUG => false,
                RequestOptions::HTTP_ERRORS => true,
                RequestOptions::JSON => [
                    'grant_type' => 'client_credentials',
                    'redirect_uri' => $service->getRedirectUrl(),
                    'client_id' => $service->getClientId(),
                    'client_secret' => $service->getClientSecret(),
                    'scope' => $service->getScope()->getScope()
                ]
            ]
        );

        $responseData = $response->getBody()->getContents();
        $responseData = Json::decode($responseData);

        return $responseData->access_token;
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

    public function findAllService(): array
    {
        return $this->entityManager->getRepository(entityName: Service::class)->findBy(
            criteria: [],
            orderBy: ['name' => Criteria::ASC]
        );
    }
}
