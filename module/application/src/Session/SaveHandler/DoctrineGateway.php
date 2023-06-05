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

    public function open($path, $name): bool
    {
        $this->sessionName = $name;

        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read($id): string
    {
        $key = $id;

        if (null === $this->session) {
            $this->session = $this->entityManager->getRepository(entityName: Session::class)->findOneBy(
                criteria: ['key' => $key]
            );
        }

        if (null !== $this->session) {
            if ($this->session->getModified() + $this->session->getLifetime() > time()) {
                //Update the lifetime again with default lifetime

                $this->session->setModified(modified: time());
                $hits = $this->session->getHits();
                $this->session->setHits(hits: $hits + 1);
                $this->entityManager->persist(entity: $this->session);
                $this->entityManager->flush();

                return $this->session->getData();
            }
            $this->destroy(id: $key);
        }

        return '';
    }

    public function destroy($id): bool
    {
        $key = $id;

        $entity = $this->entityManager->getRepository(entityName: Session::class)->findOneBy(criteria: ['key' => $key]);
        if ($entity) {
            $this->entityManager->remove(entity: $entity);
            $this->entityManager->flush();
        }

        return true;
    }

    public function write($id, $data): bool
    {
        $key = $id;

        /** @var User $user */
        $user = $data;

        $sessionRepository = $this->entityManager->getRepository(entityName: Session::class);
        if (!$session = $sessionRepository->findOneBy(criteria: ['key' => $key])) {
            $session = new Session();
        }

        $session->setUser(user: $user);
        $session->setModified(modified: time());
        $session->setData(data: (string)$user->getId());
        $session->setKey(key: $key);
        $session->setName(name: $this->sessionName);
        $session->setLifetime(lifetime: $this->lifetime);

        $this->entityManager->persist(entity: $session);
        $this->entityManager->flush();

        $this->session = $session;

        return true;
    }

    #[ReturnTypeWillChange]
    public function gc($max_lifetime): bool
    {
        return true;
    }
}
