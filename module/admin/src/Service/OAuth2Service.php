<?php

declare(strict_types=1);

namespace Admin\Service;

use Admin\Entity\User;
use Api\Entity\OAuth\AccessToken;
use Api\Entity\OAuth\Client;
use Api\Entity\OAuth\RefreshToken;
use Api\Entity\OAuth\Service;
use Application\Service\AbstractService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\RequestOptions;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Json\Json;
use OAuth2\Encryption\Jwt;

use function time;

class OAuth2Service extends AbstractService
{
    public function __construct(
        EntityManager $entityManager,
        ?TranslatorInterface $translator,
        private readonly array $config
    ) {
        parent::__construct(entityManager: $entityManager, translator: $translator);
        $this->translator = $translator;
    }

    public function findClientByClientId(string $clientId): ?Client
    {
        return $this->entityManager->getRepository(entityName: Client::class)->findOneBy(
            criteria: ['clientId' => $clientId]
        );
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
                RequestOptions::HEADERS     => [
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                RequestOptions::DEBUG       => false,
                RequestOptions::HTTP_ERRORS => true,
                RequestOptions::JSON        => [
                    'grant_type'    => 'client_credentials',
                    'redirect_uri'  => $service->getRedirectUrl(),
                    'client_id'     => $service->getClientId(),
                    'client_secret' => $service->getClientSecret(),
                    'scope'         => $service->getScope()->getScope(),
                ],
            ]
        );

        $responseData = $response->getBody()->getContents();
        $responseData = Json::decode($responseData);

        return $responseData->access_token;
    }

    public function generateJwtToken(Client $client, User $user): string
    {
        $payload = [
            'id'         => 1, // for BC (see #591)
            'jti'        => 1,
            'iss'        => 'eureka-clusters',
            'aud'        => $client->getClientId(),
            'sub'        => $user->getId(),
            'exp'        => time() + ($this->config['api-tools-oauth2']['access_lifetime'] ?? 3600),
            'iat'        => time(),
            'token_type' => $client->getPublicKey()?->getEncryptionAlgorithm(),
            'scope'      => 'openid',
        ];

        $jwtHelper = new Jwt();

        if ($client->getPublicKey()?->getEncryptionAlgorithm() === 'RS256') {
            return $jwtHelper->encode(
                payload: $payload,
                key: $client->getPublicKey()->getPrivateKey(),
                algo: $client->getPublicKey()->getEncryptionAlgorithm()
            );
        }

        return $jwtHelper->encode(
            payload: $payload,
            key: $client->getPublicKey()?->getPublicKey(),
            algo: 'HS256'
        );
    }

    public function createAccessAndRefreshToken(Client $client, User $user): array
    {
        return [
            'accessToken'  => $this->createAccessToken($client, $user)->getAccessToken(),
            'refreshToken' => $this->createRefreshToken($client, $user)->getRefreshToken(),
        ];
    }

    private function createAccessToken(Client $client, User $user): AccessToken
    {
        $accessToken = new AccessToken();
        $accessToken->setClient($client);
        $accessToken->setExpires(new \DateTimeImmutable('now + 1 hour'));
        $accessToken->setScope($client->getScope()->getScope());
        $accessToken->setUser($user);
        $accessToken->setAccessToken($this->generateAccessToken());

        $this->save($accessToken);

        return $accessToken;
    }

    private function createRefreshToken(Client $client, User $user): RefreshToken
    {
        $refreshToken = new RefreshToken();
        $refreshToken->setClient($client);
        $refreshToken->setExpires(new \DateTimeImmutable('now + 1 day'));
        $refreshToken->setScope($client->getScope()->getScope());
        $refreshToken->setUser($user);
        $refreshToken->setRefreshToken($this->generateAccessToken());

        $this->save($refreshToken);

        return $refreshToken;
    }

    private function generateAccessToken(): string
    {
        $randomData = $randomData = random_bytes(20);
        return substr(hash('sha512', $randomData), 0, 40);
    }

    public function findAllService(): array
    {
        return $this->entityManager->getRepository(entityName: Service::class)->findBy(
            criteria: [],
            orderBy: ['name' => Criteria::ASC]
        );
    }
}
