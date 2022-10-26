<?php

declare(strict_types=1);

namespace Application\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;
use Application\ValueObject\SearchFormResult;

interface FilteredObjectRepository extends ObjectRepository
{
    public function findFiltered(SearchFormResult $searchFormResult): QueryBuilder;
}
