<?php

declare(strict_types=1);

namespace Api\Enum\OAuth2;

use Application\Enum\FormElementEnumInterface;
use Application\Enum\FormElementEnumTrait;

enum ServiceTypeEnum: int implements FormElementEnumInterface
{
    use FormElementEnumTrait;

    case MICROSOFT = 1;
    case GOOGLE = 2;
    case ITEA = 3;
    case EURESCOM = 4;
    case OTHER = 5;

    public function toString(): string
    {
        return match ($this) {
            self::MICROSOFT => _('txt-microsoft'),
            self::GOOGLE    => _('txt-google'),
            self::ITEA      => _('txt-itea'),
            self::EURESCOM  => _('txt-eurescom'),
            self::OTHER     => _('txt-other'),
        };
    }

    public function hasFormField(string $field): bool
    {
        return in_array(needle: $field, haystack: $this->getFormFields(), strict: true);
    }

    public function canBeUsedAsLogin(): bool
    {
        return in_array($this, [self::ITEA, self::EURESCOM], strict: true);
    }

    public function getFormFields(): array
    {
        return match ($this) {
            self::MICROSOFT => ['tenantId', 'clientId', 'clientSecret', 'redirectUrl'],
            default         => [
                'clientId',
                'clientSecret',
                'redirectUri',
                'authorizationUrl',
                'accessTokenUrl',
                'profileUrl',
                'allowedClusters',
                'client'
            ],
        };
    }

    public function getRequiredFormFields(): array
    {
        return match ($this) {
            self::MICROSOFT => ['tenantId', 'clientId', 'clientSecret'],
            default         => [
                'clientId',
                'clientSecret',
                'redirectUrl',
                'authorizationUrl',
                'accessTokenUrl',
                'allowedClusters',
                'client'
            ],
        };
    }

    public function isMicrosoft(): bool
    {
        return $this === self::MICROSOFT;
    }
}