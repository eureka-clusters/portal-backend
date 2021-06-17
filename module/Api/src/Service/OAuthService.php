<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Api\Service;

use Application\Service\AbstractService;
use Api\Entity\OAuth\AccessToken;
use Api\Entity\OAuth\Clients;
use Api\Entity\OAuth\RefreshToken;
use Admin\Entity\User;
use OAuth2\ResponseType\AccessToken as BShafferToken;

/**
 * Class AccessToken
 * @package Api\Service
 */
class OAuthService extends AbstractService
{   
    /*

    use Bshaffer AccessToken class to generate + save Access + Refresh Token and return as array.
    returns [
        'access_token' => string '4140a526a9d25d7053ca7dae6f37e8e91c3df02e' (length=40)
        'expires_in' => int 3600
        'token_type' => string 'bearer' (length=6)
        'scope' => null
        'refresh_token' => string '80a0663c01e444356s986899835b7202c4b5fd79' (length=40)
    ];
    */
    public function createTokenForUser(User $user, string $client_id, bool $includeRefreshToken = true): ?Array
    {
        // get required storage repositories
        $clientStorage  = $this->entityManager->getRepository(Clients::class);
        $accessTokenStorage  = $this->entityManager->getRepository(AccessToken::class);
        $refreshTokenStorage  = $this->entityManager->getRepository(RefreshToken::class);
        $userStorage  = $this->entityManager->getRepository(User::class);

        // echo 'clientStorage <br>';
        // var_dump($clientStorage);
        // echo 'accessTokenStorage <br>';
        // var_dump($accessTokenStorage);
        // echo 'refreshTokenStorage <br>';
        // var_dump($refreshTokenStorage);
        // echo 'userStorage <br>';
        // var_dump($userStorage);

        // how can i have config settings in a global config?
        $config = array(
            'token_type' => 'bearer',            // token type identifier
            'access_lifetime' => 3600,           // time before access token expires
            'refresh_token_lifetime' => 1209600, // time before refresh token expires
        );

        // issue with the class name "AccessToken" either no global use or use "as" to set another name for that class.
        // $bshafferToken = new \OAuth2\ResponseType\AccessToken($accessTokenStorage, $refreshTokenStorage, $config, $includeRefreshToken);
        $bshafferToken = new BShafferToken($accessTokenStorage, $refreshTokenStorage, $config, $includeRefreshToken);
        
        $user_id = $user->getId();
        
        $tokenArray = $bshafferToken->createAccessToken($client_id, $user_id, $scope = null, $includeRefreshToken = true);

        echo 'tokenArray in OAuthService<br>';
        var_dump($tokenArray);
        return $tokenArray;


        
        /*

        $clientStorage  = $entityManager->getRepository('YourNamespace\Entity\OAuthClient');
        $userStorage = $entityManager->getRepository('YourNamespace\Entity\OAuthUser');
        $accessTokenStorage = $entityManager->getRepository('YourNamespace\Entity\OAuthAccessToken');

        // Pass the doctrine storage objects to the OAuth2 server class
        $server = new \OAuth2\Server([
            'client_credentials' => $clientStorage,
            'user_credentials'   => $userStorage,
            'access_token'       => $accessTokenStorage,
        ], [
            'auth_code_lifetime' => 30,
            'refresh_token_lifetime' => 30,
        ]);
        */


    }


    public function createTokenForUserManual(User $user, string $client_id): ?AccessToken
    {

        // get existing accessToken for user + client
        $accessToken = $this->entityManager->getRepository(AccessToken::class)->findOneBy(
            [
                'user' => $user->getId(),
                'clientId' => $client_id
            ]
        );

        // create a new one if it doesn't exists.        
        if (null === $accessToken) {
            $accessToken = new AccessToken();
            $accessToken->setUser($user);
            $accessToken->setClientId($client_id);
        }

        // reuse of tokens?  set a new accessToken ?
        // or should the already existing one still persist?

        // it could also be checked if the token is expired and only create a new one then.
        // if ($accessToken->isExpired()) {
        // }

        // generate a new AccessToken
        $accessToken->setAccessToken($this->generateAccessToken());

        // how can something like the lifetime of the token be set in a module config?
        // var_dump($this->config); 

        //is there a way to use this classes?
        //'Laminas\\ApiTools\\OAuth2',
        //'Laminas\\ApiTools\\MvcAuth',
        // or 'api-tools-mvc-auth' from the api module?
        
        // just generates the token with 'access_token'.. how can a randomized access_token be generated...
        // $options = ['access_token'=>null, 'expires_in' => 100]; // access_token is required
        $options = ['access_token' => 'access_token', 'expires_in' => 100];
        $accessToken2 = new \League\OAuth2\Client\Token\AccessToken($options);
        var_dump($accessToken2);
        // which couldn't be saved through entityManager
        // $this->entityManager->persist($accessToken2);
        
        $accessToken->setExpires(new \DateTimeImmutable('+1 day'));

        $this->entityManager->persist($accessToken);
        $this->entityManager->flush();
        return $accessToken;
    }

    // function from bshaffer directly
    /**
     * Generates an unique access token.
     *
     * Implementing classes may want to override this function to implement
     * other access token generation schemes.
     *
     * @return string - A unique access token.
     *
     * @ingroup oauth2_section_4
     */
    protected function generateAccessToken()
    {
        if (function_exists('random_bytes')) {
            $randomData = random_bytes(20);
            if ($randomData !== false && strlen($randomData) === 20) {
                return bin2hex($randomData);
            }
        }
        if (function_exists('openssl_random_pseudo_bytes')) {
            $randomData = openssl_random_pseudo_bytes(20);
            if ($randomData !== false && strlen($randomData) === 20) {
                return bin2hex($randomData);
            }
        }
        if (function_exists('mcrypt_create_iv')) {
            $randomData = mcrypt_create_iv(20, MCRYPT_DEV_URANDOM);
            if ($randomData !== false && strlen($randomData) === 20) {
                return bin2hex($randomData);
            }
        }
        if (@file_exists('/dev/urandom')) { // Get 100 bytes of random data
            $randomData = file_get_contents('/dev/urandom', false, null, 0, 20);
            if ($randomData !== false && strlen($randomData) === 20) {
                return bin2hex($randomData);
            }
        }
        // Last resort which you probably should just get rid of:
        $randomData = mt_rand() . mt_rand() . mt_rand() . mt_rand() . microtime(true) . uniqid(mt_rand(), true);

        return substr(hash('sha512', $randomData), 0, 40);
    }

    /**
     * Generates an unique refresh token
     *
     * Implementing classes may want to override this function to implement
     * other refresh token generation schemes.
     *
     * @return string - A unique refresh token.
     *
     * @ingroup oauth2_section_4
     * @see OAuth2::generateAccessToken()
     */
    protected function generateRefreshToken()
    {
        return $this->generateAccessToken(); // let's reuse the same scheme for token generation
    }
}
