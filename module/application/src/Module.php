<?php

declare(strict_types=1);

namespace Application;

use Api\Service\OAuthService;
use Interop\Container\ContainerInterface;
use Laminas\ApiTools\MvcAuth\Identity\GuestIdentity;
use Laminas\ApiTools\MvcAuth\MvcAuthEvent;
use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature;
use OAuth2\Encryption\Jwt;

final class Module implements Feature\ConfigProviderInterface, Feature\BootstrapListenerInterface
{
    private ContainerInterface $container;

    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(EventInterface $e): void
    {
        $this->container = $e->getApplication()->getServiceManager();
        $app             = $e->getTarget();
        $events          = $app->getEventManager();
        $events->attach('authentication', [$this, 'onAuthentication'], 100);
    }

    public function onAuthentication(MvcAuthEvent $e)
    {
        $guest  = new GuestIdentity();
        $header = $e->getMvcEvent()->getRequest()->getHeader('Authorization');

        if (!$header) {
            $e->setIdentity($guest);
            return $guest;
        }

        $token = $header->getFieldValue();

        //Skip this event when we found a Bearer token
        if (str_starts_with($token, 'Bearer')) {
            return;
        }

        /** @var OAuthService $oauthService */
        $oauthService = $this->container->get(OAuthService::class);

        //Try to find the JWT Token
        $jwtToken = $oauthService->findJWTTokenByToken($token);

        if (null === $jwtToken) {
            //We return nothing so the event manager continues to the next operation
            $e->setIdentity($guest);
            return $guest;
        }

        $jwt       = new Jwt();
        $tokenData = $jwt->decode($jwtToken->getToken(), $jwtToken->getClient()?->getJwtKey());

        // If the token is invalid, give up
        if (!$tokenData) {
            //We return nothing so the event manager continues to the next operation
            $e->setIdentity($guest);
            return $guest;
        }

        //We use the name here
        $identity = new \Laminas\ApiTools\MvcAuth\Identity\AuthenticatedIdentity($tokenData['id']);
        $identity->setName((string)$tokenData['id']);

        $e->getMvcEvent()->setParam('Laminas\ApiTools\MvcAuth\Identity', $identity);

        return $identity;
    }
}
