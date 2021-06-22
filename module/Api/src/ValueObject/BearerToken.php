<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */
declare(strict_types=1);

namespace Api\ValueObject;

final class BearerToken
{
    private string $accessToken;
    private int    $expiresIn;
    private string $tokenType;
    private string $scope;
    private string $refreshToken;

    public function __construct(
        string $accessToken,
        int $expiresIn,
        string $tokenType,
        ?string $scope,
        string $refreshToken
    ) {
        $this->accessToken  = $accessToken;
        $this->expiresIn    = $expiresIn;
        $this->tokenType    = $tokenType;
        $this->scope        = $scope;
        $this->refreshToken = $refreshToken;
    }

    public static function fromArray(array $params): BearerToken
    {
        return new self(
            $params['accessToken'] ?? '',
            $params['expiresIn'] ?? 3600,
            $params['tokenType'] ?? '',
            $params['scope'] ?? '',
            $params['refreshToken'] ?? '',
        );
    }

    public function toArray(): array
    {
        return [
            'access_token'  => $this->accessToken,
            'expires_in'    => $this->expiresIn,
            'token_type'    => $this->tokenType,
            'scope'         => $this->scope,
            'refresh_token' => $this->refreshToken
        ];
    }
}
