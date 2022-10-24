<?php

declare(strict_types=1);

namespace Application\Controller;

use Admin\Service\OAuth2Service;
use Admin\Service\UserService;
use Application\ValueObject\OAuth2\GenericUser;
use DateInterval;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\Container;
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

    public function loginAction(): Response
    {
        //Find the service
        $service = $this->params('service');
        $clientId = $this->getRequest()->getQuery(name: 'client');

        //And grab the settings
        $settings = $this->config['oauth2-settings']['services'][$service] ?? [];

        if (empty($settings) || !isset($settings['settings'])) {
            return $this->redirect()->toRoute(route: 'home');
        }

        $oAuthClient = new GenericProvider(options: $settings['settings']);

        $authUrl = $oAuthClient->getAuthorizationUrl();

        //Create a session and keep the important data
        $session = new Container(name: 'session');
        $session->authState = $oAuthClient->getState();
        $session->service = $service;
        $session->clientId = $clientId;
        $session->settings = $settings['settings'];
        $session->profileUrl = $settings['profileUrl'] ?? null;

        return $this->redirect()->toUrl(url: $authUrl);
    }

    public function callbackAction(): Response
    {
        $session = new Container(name: 'session');
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

        if (null !== $authCode) {
            try {
                //And grab the settings and grab a token to check the backend, so we can
                $oAuthBackendClient = new GenericProvider(options: $session->settings);

                $accessToken = $oAuthBackendClient->getAccessToken(
                    grant: 'authorization_code',
                    options: [
                        'code' => $authCode,
                    ]
                );

                //Do a manual Guzzle Request for better debugging
                $guzzle = new Client();
                $request = $guzzle->request(
                    method: 'GET',
                    uri: $session->profileUrl,
                    options: [
                        RequestOptions::HEADERS => [
                            'Authorization' => 'Bearer ' . $accessToken,
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json',
                        ],
                        RequestOptions::DEBUG => false,
                        RequestOptions::HTTP_ERRORS => true,
                    ]
                );

                // get the GenericUser but filter the cluster_permissions with the allowedCluster setting of the oauth2-settings for the used service
                $genericUser = GenericUser::fromJson(
                    jsonString: $request->getBody()->getContents(),
                    allowedClusters: $session->settings['allowedClusters']
                );

                //find or create new user by the returned User information
                $user = $this->userService->findOrCreateUserFromGenericUser(
                    genericUser: $genericUser,
                    allowedClusters: $session->settings['allowedClusters']
                );

                //Find the oAuth client to redirect to the frontend
                $oAuthClient = $this->oAuth2Service->findLatestClient();

                $token = $this->oAuth2Service->generateJwtToken(client: $oAuthClient, user: $user);

                //Redirect to frontend
                return $this->redirect()->toUrl(
                    url: $oAuthClient->getRedirectUri() . '?token=' . $token
                );
            } catch (IdentityProviderException) {
                return $this->redirect()->toRoute(route: '/');
            }
        }

        return $this->redirect()->toRoute(route: 'home');
    }
}
