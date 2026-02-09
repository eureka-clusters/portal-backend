<?php

namespace Application\Enum;

trait FormElementEnumTrait
{
    public static function toArray(): array
    {
        $results = [];

        foreach (static::cases() as $case) {
            $results[$case->value] = $case->toString();
        }

        arsort(array: $results);

        return $results;
    }
}