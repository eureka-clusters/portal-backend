<?php

declare(strict_types=1);

namespace Application;

use Admin\Service\UserService;
use Api\Options\ModuleOptions;
use Application\Authentication\AuthenticatedIdentity;
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

    /**
     * If the AUTHORIZATION HTTP header is found, validate and return the user,
     * otherwise default to 'guest'
     *
     * @param MvcAuthEvent $e
     * @return AuthenticatedIdentity|GuestIdentity
     */
    public function onAuthentication(MvcAuthEvent $e)
    {
        $guest  = new GuestIdentity();
        $header = $e->getMvcEvent()->getRequest()->getHeader('Authorization');

        if (!$header) {
            $e->setIdentity($guest);
            return $guest;
        }

        $token = $header->getFieldValue();
        $jwt   = new Jwt();

        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $this->container->get(ModuleOptions::class);

        $tokenData = $jwt->decode($token, $moduleOptions->getCryptoKey());

        // If the token is invalid, give up
        if (!$tokenData) {
            $e->setIdentity($guest);

            return $guest;
        }

        /** @var UserService $userService */
        $userService = $this->container->get(UserService::class);
        $user        = $userService->findUserById($tokenData['id']);

        $e->getMvcEvent()->setParam('Laminas\ApiTools\MvcAuth\Identity', new AuthenticatedIdentity($user));

        return new AuthenticatedIdentity($user);
    }
}
