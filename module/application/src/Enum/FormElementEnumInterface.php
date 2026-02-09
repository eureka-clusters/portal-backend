<?php

namespace Application\Enum;

interface FormElementEnumInterface
{
    public static function toArray(): array;

    public function toString(): string;
}