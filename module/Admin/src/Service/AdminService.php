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

use Admin\Entity\Permit;
use Admin\Entity\Permit\Role as PermitRole;
use Admin\Entity\Role;
use Admin\Entity\Template;
use Admin\Entity\User;
use Admin\Entity;
use Admin\Repository;
use Application\Service\AbstractService;
use Doctrine\Common\Cache\RedisCache;
use Doctrine\ORM\EntityManager;
use Laminas\I18n\Translator\TranslatorInterface;

use function array_map;
use function array_merge;
use function array_unique;
use function count;
use function sprintf;

/**
 * Class AdminService
 *
 * @package Admin\Service
 */
class AdminService extends AbstractService
{

}
