<?php

declare(strict_types=1);

namespace Application\Authentication\Storage;

use Admin\Entity\User;
use Admin\Service\UserService;
use Application\Session\SaveHandler\DoctrineGateway;
use JetBrains\PhpStorm\Pure;
use Laminas\Authentication\Storage\Session;
use Laminas\Session\Config\SessionConfig;
use Laminas\Session\ManagerInterface;

class AuthenticationStorage extends Session
{
    protected ?bool $isEmpty = null;

    protected ?User $resolvedIdentity = null;

    public function __construct(protected DoctrineGateway $saveHandler, private readonly UserService $userService)
    {
        parent::__construct('PA_PORTAL_BACKEND_AUTH');

        //open session
        $sessionConfig = new SessionConfig();
        $this->saveHandler->open($sessionConfig->getOption('save_path'), 'PA_PORTAL_BACKEND_AUTH');
        //set save handler with configured session
        $this->session->getManager()->setSaveHandler($this->saveHandler);
    }

    public function isEmpty(): bool
    {
        if (null === $this->isEmpty) {
            $this->isEmpty = empty($this->saveHandler->read($this->getSessionId()));
        }

        return $this->isEmpty;
    }

    public function getSessionId(): string
    {
        return $this->session->getManager()->getId();
    }

    /**
     * @param User $contents
     */
    public function write($contents): void
    {
        //Force isempty to become true
        $this->isEmpty = false;

        $this->saveHandler->write($this->getSessionId(), $contents);
    }

    public function read(): ?User
    {
        if (null !== $this->resolvedIdentity) {
            return $this->resolvedIdentity;
        }

        $identity = (int) $this->saveHandler->read($this->getSessionId());
        $identity = $this->userService->findUserById((int) $identity);

        if ($identity) {
            $this->resolvedIdentity = $identity;
        }

        return $this->resolvedIdentity;
    }

    public function clear(): void
    {
        $this->saveHandler->destroy($this->getSessionId());

        parent::clear();
    }

    #[Pure] public function getSessionManager(): ManagerInterface
    {
        return $this->session->getManager();
    }
}
