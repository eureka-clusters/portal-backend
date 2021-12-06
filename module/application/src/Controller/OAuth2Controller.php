<?php

declare(strict_types=1);

namespace Application\Controller;

use Admin\Service\UserService;
use Api\Service\OAuthService;
use Application\ValueObject\OAuth2\GenericUser;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\Container;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;

final class OAuth2Controller extends AbstractActionController
{
    public function __construct(
        private UserService $userService,
        private OAuthService $oAuthService,
        private array $config
    ) {
    }

    public function loginAction(): Response
    {
        //Find the service
        $service = $this->params('service');

        //And grab the settings
        $settings = $this->config['oauth2-settings']['services'][$service] ?? [];

        if (empty($settings) || !isset($settings['settings'])) {
            return $this->redirect()->toRoute('home');
        }

        $oAuthClient = new GenericProvider($settings['settings']);

        $authUrl = $oAuthClient->getAuthorizationUrl();

        //Create a session and keep the important data
        $session             = new Container('session');
        $session->authState  = $oAuthClient->getState();
        $session->service    = $service;
        $session->settings   = $settings['settings'];
        $session->profileUrl = $settings['profileUrl'] ?? null;

        return $this->redirect()->toUrl($authUrl);
    }

    public function callbackAction(): Response
    {
        $session       = new Container('session');
        $expectedState = $session->authState;

        $providedState = $this->getRequest()->getQuery('state');

        if (null === $expectedState) {
            return $this->redirect()->toRoute('home');
        }

        if (null === $providedState || $expectedState !== $providedState) {
            return $this->redirect()->toRoute('home');
        }

        $error = $this->getRequest()->getQuery('error');

        if ($error !== null) {
            die('error on oauth Authorize');
        }

        $authCode = $this->getRequest()->getQuery('code');

        if (null !== $authCode) {
            try {
                //And grab the settings and grab a token to check the backend so we can
                $oAuthBackendClient = new GenericProvider($session->settings);

                $accessToken = $oAuthBackendClient->getAccessToken(
                    'authorization_code',
                    [
                        'code' => $authCode,
                    ]
                );

                //Do a manual Guzzle Request for better debugging
                $guzzle  = new Client();
                $request = $guzzle->request(
                    'GET',
                    $session->profileUrl,
                    [
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
                    $request->getBody()->getContents(),
                    $session->settings['allowedClusters']
                );

                //find or create new user by the returned User information
                $user = $this->userService->findOrCreateUserFromGenericUser(
                    $genericUser,
                    $session->settings['allowedClusters']
                );

                $client = $this->oAuthService->findDefaultJWTClient();
                //We need to find the client and we use the generic client
                $token = $this->oAuthService->findOrGenereateJWTToken($user);

                //Redirect to frontend
                return $this->redirect()->toUrl(
                    $client->getRedirectUri() . '?token=' . $token->getToken()
                );
            } catch (IdentityProviderException $e) {
                return $this->redirect()->toRoute('user/login');
            }
        }

        return $this->redirect()->toRoute('home');
    }
}
