<?php
/**
 * Jield BV all rights reserved
 *
 * @author      Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2021 Jield BV (https://jield.nl)
 */

namespace Application\Authentication\OAuth2\Adapter;

use Admin\Entity\User;
use Doctrine\ORM\EntityManager;
use Laminas\Crypt\Password\Bcrypt;

class PdoAdapter extends \Laminas\ApiTools\OAuth2\Adapter\PdoAdapter
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager, $config = [])
    {
        //Inject the connection our own Doctrine instance
        parent::__construct($entityManager->getConnection()->getWrappedConnection(), $config);
        if (isset($config['bcrypt_cost'])) {
            $this->setBcryptCost($config['bcrypt_cost']);
        }

        $this->entityManager = $entityManager;
    }

    public function checkClientCredentials($clientId, $clientSecret = null): bool
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['userPrincipalName' => $clientId]);

        if (null === $user) {
            return false;
        }

        //for new uses, the password is null, so we just set the PW
        if (null === $user->getPassword()) {
            //Generate a Bcrypted PWD
            $bcrypt = new Bcrypt();
            $user->setPassword($bcrypt->create($clientSecret));
            $this->entityManager->persist($user);
            $this->entityManager->flush($user);
        }

        return $this->verifyHash($clientSecret, $user->getPassword());
    }

    public function checkUserCredentials($userPrincipalName, $password): bool
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(
            ['userPrincipalName' => $userPrincipalName]
        );

        if (null === $user) {
            return false;
        }

        return $this->verifyHash($password, $user->getPassword());
    }

    public function getUserDetails($userPrincipalName)
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(
            ['userPrincipalName' => $userPrincipalName]
        );

        if (null === $user) {
            return false;
        }

        return [
            'user_id'  => $user->getId(),
            'username' => $user->getUsername()
        ];
    }

    public function getClientDetails($clientId)
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['userPrincipalName' => $clientId]);

        if (null === $user) {
            return false;
        }


        return [
            'user_id'  => $user->getId(),
            'username' => $user->getUsername()
        ];
    }
}
