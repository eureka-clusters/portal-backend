<?php

declare(strict_types=1);

namespace Application\Session\SaveHandler;

use Admin\Entity\Session;
use Admin\Entity\User;
use Doctrine\ORM\EntityManager;
use Laminas\Session\SaveHandler\SaveHandlerInterface;
use ReturnTypeWillChange;

use function time;

class DoctrineGateway implements SaveHandlerInterface
{
    private ?Session $session = null;
    private string $sessionName;
    private readonly int $lifetime;

    public function __construct(private readonly EntityManager $entityManager, array $config)
    {
        $this->lifetime = $config['session_config']['cookie_lifetime'] ?? 31_536_000;
    }

    public function open($savePath, $name): bool
    {
        $this->sessionName = $name;

        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read($key): string
    {
        if (null === $this->session) {
            $this->session = $this->entityManager->getRepository(Session::class)->findOneBy(['key' => $key]);
        }

        if (null !== $this->session) {
            if ($this->session->getModified() + $this->session->getLifetime() > time()) {
                //Update the lifetime again with default lifetime

                $this->session->setModified(time());
                $hits = $this->session->getHits();
                $this->session->setHits($hits + 1);
                $this->entityManager->persist($this->session);
                $this->entityManager->flush();

                return $this->session->getData();
            }
            $this->destroy($key);
        }

        return '';
    }

    public function destroy($key): bool
    {
        $entity = $this->entityManager->getRepository(Session::class)->findOneBy(['key' => $key]);
        if ($entity) {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        }

        return true;
    }

    /**
     * @param string $key
     * @param User $user
     */
    public function write($key, $user): bool
    {
        $sessionRepository = $this->entityManager->getRepository(Session::class);
        if (! $session = $sessionRepository->findOneBy(['key' => $key])) {
            $session = new Session();
        }

        $session->setUser($user);
        $session->setModified(time());
        $session->setData((string) $user->getId());
        $session->setKey($key);
        $session->setName($this->sessionName);
        $session->setLifetime($this->lifetime);

        $this->entityManager->persist($session);
        $this->entityManager->flush();

        $this->session = $session;

        return true;
    }

    #[ReturnTypeWillChange]
    public function gc($maxLifetime): bool
    {
        return true;
    }
}
