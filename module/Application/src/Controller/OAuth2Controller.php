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
use Application\ValueObject\OAuth2\GenericUser;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\Container;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;
use Api\Service\OAuthService;


/**
 * Class OAuth2Controller
 * @package Application\Controller
 */
final class OAuth2Controller extends AbstractActionController
{
    private UserService           $userService;
    private array                 $config;
    private AuthenticationService $authenticationService;

    public function __construct(UserService $userService, OAuthService $oauthService, array $config, AuthenticationService $authenticationService)
    {
        $this->userService           = $userService;
        $this->oauthService          = $oauthService;
        $this->config                = $config;
        $this->authenticationService = $authenticationService;
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
                
                // echo 'accessToken <br>';
                // var_dump($accessToken);
                // var_dump($session->profileUrl);
                // var_dump($session);

                $session->accessToken = $accessToken;

                // the api could also be requested through the oauthClient so no addional guzzle request object could be required.
                // $request = $oAuthClient->getAuthenticatedRequest(
                //     'GET',
                //     $session->profileUrl,
                //     $accessToken,
                //     [
                //         'Accept'        => 'application/json',
                //         'Content-Type'  => 'application/json'
                //     ]
                // );
                // var_dump($request);
                // $response = $oAuthClient->getParsedResponse($request);
                // var_dump($response);
                // die();


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
                
                // only test as my current api/me returns multiple clusters instead of the cluster "application" name tbd. 
                $genericUser->setCluster('celtic');
                // echo 'genericUser <br>';
                // var_dump($genericUser);

                //find or create new user by the returned User information
                $user = $this->userService->findOrCreateUserFromGenericUser($genericUser);

                //get the settings for the react client
                $reactSettings = $this->config['oauth2-settings']['services']['reactclient']['settings'];
                // echo 'settings react client<br>';
                // var_dump($reactSettings);

                // can the react client details be used on itself to generate a user token?
                // $reactOAuthClient = new GenericProvider($reactSettings);
                // $reactAccessToken = $reactOAuthClient->getAccessToken('client_credentials');
                // echo 'reactAccessToken<br>';
                // var_dump($reactAccessToken);
                // doesn't work!
                // Invalid response received from Authorization Server. Expected JSON.
                // die();

                // test to get a new token from the cluster application via refreshToken => works 
                // so this could be used to refresh the login after the token has expired.
                // $newAccessToken = $oAuthClient->getAccessToken('refresh_token', [
                //     'refresh_token' => $accessToken->getRefreshToken()
                // ]);
                // echo 'newAccessToken <br>';
                // var_dump($newAccessToken);


                //create a bearer token for the user + cluster + client ="reactclient'
                $reactToken = $this->oauthService->createTokenForUser($user, 'reactclient');
                echo 'reactToken <br>';
                var_dump($reactToken);

                //create the db entry manually
                $reactTokenManual = $this->oauthService->createTokenForUserManual($user, 'reactclient');
                echo 'reactTokenManual <br>';
                var_dump($reactTokenManual);


                // how is the token returned to the react client?
                // should the parameters added to the redirectUri?
                
                //Redirect to frontend
                return $this->redirect()->toRoute('home');
            } catch (IdentityProviderException $e) {
                var_dump($e);
                //  return $this->redirect()->toRoute('user/login');
            }
        }

        return $this->redirect()->toRoute('home');
    }
}
