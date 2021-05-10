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
}
