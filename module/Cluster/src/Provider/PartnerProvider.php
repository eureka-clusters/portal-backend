<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Provider;

use Cluster\Entity;
use Cluster\Service\PartnerService;
use Doctrine\Common\Cache\RedisCache;

/**
 * Class ProjectProvider
 * @package Project\Provider
 */
class PartnerProvider
{
    private RedisCache     $redisCache;
    private PartnerService $partnerService;

    public function __construct(RedisCache $redisCache, PartnerService $partnerService)
    {
        $this->redisCache     = $redisCache;
        $this->partnerService = $partnerService;
    }

    public function generateArray(Entity\Statistics\Partner $partner): array
    {
        return ['test'];
    }
}
