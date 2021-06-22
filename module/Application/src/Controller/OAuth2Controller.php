<?php

/**
 * Jield BV all rights reserved
 *
 * @author      Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2021 Jield BV (https://jield.nl)
 */

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

/**
 * Class OAuth2Controller
 * @package Application\Controller
 */
final class OAuth2Controller extends AbstractActionController
{
    private UserService  $userService;
    private array        $config;
    private OAuthService $oAuthService;

    public function __construct(UserService $userService, OAuthService $oAuthService, array $config)
    {
        $this->userService  = $userService;
        $this->oAuthService = $oAuthService;
        $this->config       = $config;
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
        $authUrl     = $oAuthClient->getAuthorizationUrl();

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
            var_dump($error);
            die('error on oauth Authorize');
        }

        // if ($error === 'access_denied') {
        //     return $this->redirect()->toRoute('home');
        // }

        // no handling for other errors e.g.
        // 'error=invalid_scope&error_description=An+unsupported+scope+was+requested&state=1184dd44eb0364f72d9694745fd7a64e

        // @Johan how are errors handled?
        // should they be returned or echoed on the middleware
        // perhaps the error should be shown on the react app as the react app calls this action?

        $authCode = $this->getRequest()->getQuery('code');

        if (null !== $authCode) {
            try {
                //And grab the settings
                $oAuthClient = new GenericProvider($session->settings);

                $accessToken = $oAuthClient->getAccessToken(
                    'authorization_code',
                    [
                        'code' => $authCode
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
                            'Content-Type'  => 'application/json'
                        ],
                        RequestOptions::DEBUG       => false,
                        RequestOptions::HTTP_ERRORS => true,
                    ]
                );

                $genericUser = GenericUser::fromJson($request->getBody()->getContents());

                //find or create new user by the returned User information
                $user = $this->userService->findOrCreateUserFromGenericUser($genericUser);

                $oAuthClient = $this->oAuthService->findoAuthClientByClientId('reactclient');

                $reactToken = $this->oAuthService->createTokenForUser($user, $oAuthClient);

                //Redirect to frontend
                return $this->redirect()->toUrl($oAuthClient->getRedirectUri());
            } catch (IdentityProviderException $e) {
                var_dump($e);
                //  return $this->redirect()->toRoute('user/login');
            }
        }

        return $this->redirect()->toRoute('home');
    }
}
