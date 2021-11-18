<?php

declare(strict_types=1);

namespace Api\Options;

use Laminas\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions
{
    protected int $accessTokenLifetime       = 3600;      // 1 hour
    protected int $refreshTokenLifetime      = 1209600;   // 14 days
    protected int $authorizationCodeLifetime = 300;   // 5 minutes

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

    public function getAuthorizationCodeLifetime(): int
    {
        return $this->authorizationCodeLifetime;
    }

    public function setAuthorizationCodeLifetime(int $authorizationCodeLifetime): ModuleOptions
    {
        $this->authorizationCodeLifetime = $authorizationCodeLifetime;
        return $this;
    }
}
