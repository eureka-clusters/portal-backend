<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Admin\Service;

use Admin\Entity\User;
use Application\Service\AbstractService;

/**
 * Class UserService
 *
 * @package Admin\Service
 */
class UserService extends AbstractService
{
    public function findUserById(int $id): ?User
    {
        return $this->entityManager->find(User::class, $id);
    }
}
