<?php

declare(strict_types=1);

namespace Application\Helper;

use DomainException;
use Error;
use InvalidArgumentException;
use Random\RandomException;

final class RandomHelper
{
    /**
     * @throws RandomException
     */
    public static function getString(int $length = 15): string
    {
        $hash = bin2hex(string: self::getBytes(length: 100));
        return substr(string: $hash, offset: 0, length: $length);
    }

    public static function getBytes(int $length): string
    {
        try {
            return random_bytes(length: $length);
        } catch (RandomException $randomException) {
            throw new InvalidArgumentException(
                message:  'Invalid parameter provided to getBytes(length)',
                code:     0,
                previous: $randomException
            );
        }
    }
}