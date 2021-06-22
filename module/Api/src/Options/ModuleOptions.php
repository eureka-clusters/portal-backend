<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Api\Options;

use Laminas\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions
{
    protected int $accessTokenLifetime  = 3600;
    protected int $refreshTokenLifetime = 1209600;

    public function getAccessTokenLifetime(): int
    {
        return $this->accessTokenLifetime;
    }

    public function setAccessTokenLifetime(int $accessTokenLifetime): ModuleOptions
    {
        $this->accessTokenLifetime = $accessTokenLifetime;
        return $this;
    }

    public function getRefreshTokenLifetime(): int
    {
        return $this->refreshTokenLifetime;
    }

    public function setRefreshTokenLifetime(int $refreshTokenLifetime): ModuleOptions
    {
        $this->refreshTokenLifetime = $refreshTokenLifetime;
        return $this;
    }
}
