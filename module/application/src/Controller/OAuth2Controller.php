<?php

declare(strict_types=1);

namespace Application\Controller;

use Admin\Service\OAuth2Service;
use Admin\Service\UserService;
use Api\Entity\OAuth\Service;
use Application\ValueObject\OAuth2\GenericUser;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Laminas\Http\Response;
use Laminas\Json\Json;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\Container;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;
use OAuth2\Encryption\Jwt;

final class OAuth2Controller extends AbstractActionController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly OAuth2Service $oAuth2Service,
        private readonly array $config
    ) {
    }

    public function loginAction(): Response|ViewModel
    {
        //Find the service
        $id      = (int)$this->params('id');
        $service = $this->oAuth2Service->findServiceById(id: $id);

        if (null === $service) {
            return $this->notFoundAction();
        }

        $oAuthClient = new GenericProvider(options: $service->parseOptions());

        $authUrl = $oAuthClient->getAuthorizationUrl();

        //Create a session and keep the important data
        $session            = new Container(name: 'session');
        $session->authState = $oAuthClient->getState();
        $session->serviceId = $service->getId();

        return $this->redirect()->toUrl(url: $authUrl);
    }

    public function callbackAction(): Response
    {
        $session       = new Container(name: 'session');
        $expectedState = $session->authState;

        $providedState = $this->getRequest()->getQuery(name: 'state');

        if (null === $expectedState) {
            return $this->redirect()->toRoute(route: 'home');
        }

        if (null === $providedState || $expectedState !== $providedState) {
            return $this->redirect()->toRoute(route: 'home');
        }

        $error = $this->getRequest()->getQuery(name: 'error');

        if ($error !== null) {
            die('error on oauth Authorize');
        }

        $authCode = $this->getRequest()->getQuery(name: 'code');

        /** @var Service $service */
        $service = $this->oAuth2Service->findServiceById(id: $session->serviceId);

        if (null !== $authCode) {
            try {
                //And grab the settings and grab a token to check the backend, so we can
                $oAuthBackendClient = new GenericProvider(options: $service->parseOptions());

                $accessToken = $oAuthBackendClient->getAccessToken(
                    grant: 'authorization_code',
                    options: [
                        'code' => $authCode,
                    ]
                );

                //Do a manual Guzzle Request for better debugging
                $guzzle  = new Client();
                $request = $guzzle->request(
                    method: 'GET',
                    uri: $service->getProfileUrl(),
                    options: [
                        RequestOptions::HEADERS     => [
                            'Authorization' => 'Bearer ' . $accessToken,
                            'Accept'        => 'application/json',
                            'Content-Type'  => 'application/json',
                        ],
                        RequestOptions::DEBUG       => false,
                        RequestOptions::HTTP_ERRORS => true,
                    ]
                );

                // get the GenericUser but filter the cluster_permissions with the allowedCluster setting of the oauth2-settings for the used service
                $genericUser = GenericUser::fromJson(
                    jsonString: $request->getBody()->getContents(),
                    allowedClusters: $service->getAllowedClusters()->map(
                        fn ($cluster) => $cluster->getName()
                    )->toArray()
                );

                //find or create new user by the returned User information
                $user = $this->userService->findOrCreateUserFromGenericUser(
                    genericUser: $genericUser,
                    allowedClusters: $service->getAllowedClusters()->map(
                        fn ($cluster) => $cluster->getName()
                    )->toArray()
                );

                //We let the system create an access token and a refresh token
                $token = $this->oAuth2Service->generateJwtToken(client: $service->getClient(), user: $user);

                //Redirect to frontend with the tokens
                return $this->redirect()->toUrl(
                    url: $service->getClient()->getRedirectUri(
                    ) . '?token=' . $token . '&client_id=' . $service->getClient()->getClientId()
                );
            } catch (IdentityProviderException) {
                return $this->redirect()->toRoute(route: '/');
            }
        }

        return $this->redirect()->toRoute(route: 'home');
    }

    public function refreshAction(): JsonModel|Response
    {
        $content = $this->getRequest()->getContent();

        try {
            $data        = Json::decode(encodedValue: $content);
            $clientId    = $data->client_id ?? null;
            $bearerToken = $data->token ?? null;
        } catch (\Exception) {
            return $this->getResponse()->setStatusCode(code: 400)->setContent('Invalid Client');
        }

        if (null === $clientId) {
            return $this->getResponse()->setStatusCode(code: 400)->setContent('Empty Client ID');
        }

        $client = $this->oAuth2Service->findClientByClientId(clientId: $clientId);

        if (null === $client) {
            return $this->getResponse()->setStatusCode(code: 400)->setContent('Invalid Client');
        }

        //Try to decode the token
        try {
            $jwtHelper = new Jwt();
            $key       = $jwtHelper->decode(
                jwt: $bearerToken,
                key: $client->getPublicKey()?->getPublicKey(),
                allowedAlgorithms: [$client->getPublicKey()?->getEncryptionAlgorithm()]
            );

            if (!$key) {
                return $this->getResponse()->setStatusCode(code: 400)->setContent('Invalid key');
            }

            $key['exp'] = time() + ($this->config['api-tools-oauth2']['access_lifetime'] ?? 3600);
            $key['int'] = time();

            return new JsonModel(variables: [
                'success' => true,
                'token'   => $jwtHelper->encode(
                    payload: $key,
                    key: $client->getPublicKey()?->getPrivateKey(),
                    algo: $client->getPublicKey()?->getEncryptionAlgorithm()
                ),
            ]);
        } catch (\Exception) {
            return $this->getResponse()->setStatusCode(code: 400)->setContent('Invalid token');
        }
    }
}
