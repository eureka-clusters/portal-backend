<?php

declare(strict_types=1);

namespace Application\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;

interface FilteredObjectRepository extends ObjectRepository
{
    public function findFiltered(array $filter = []): QueryBuilder;
}
