<?php
/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

namespace Application\Authentication\OAuth2\Adapter;

use Doctrine\ORM\EntityManager;

final class PdoAdapter extends \Laminas\ApiTools\OAuth2\Adapter\PdoAdapter
{
    public function __construct(EntityManager $entityManager, $config = [])
    {
        //Inject the connection our own Doctrine instance
        parent::__construct($entityManager->getConnection()->getWrappedConnection(), $config);
    }
}
