<?php

declare(strict_types=1);

namespace Application\Repository;

use Jield\Search\ValueObject\SearchFormResult;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;

interface FilteredObjectRepository extends ObjectRepository
{
    public function findFiltered(SearchFormResult $searchFormResult): QueryBuilder;
}
