<?php

declare(strict_types=1);

namespace Api\Enum;

enum RouteTypeEnum: string
{
    case ENTITY = 'entity';
    case COLLECTION = 'collection';
}
