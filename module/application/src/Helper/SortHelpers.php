<?php

namespace Application\Helper;

use Application\Entity\AbstractEntity;
use Doctrine\Common\Collections\Collection;
use Webmozart\Assert\Assert;

class SortHelpers
{
    public static function createReorderLookupArray(array|Collection $entities, int $movedId, int $oldSequence, int $newSequence): array
    {
        Assert::allIsInstanceOf(value: $entities, class: AbstractEntity::class, message: 'We can only reorder entities that are instances of AbstractEntity');

        //Create an array with all values with the new sequence having increments of 10
        $lookupArray = [];
        $key         = 0;
        foreach ($entities as $entity) {
            $lookupArray[$entity->getId()] = $key;
            $key                           += 10;
        }

        $movedUp = $newSequence < $oldSequence;

        $sequence = ($newSequence * 10) + 1;

        if ($movedUp) {
            $sequence = ($newSequence * 10) - 1;
        }

        //Now we save the new sequence
        $lookupArray[$movedId] = $sequence;

        //Sort the array on values
        asort(array: $lookupArray);

        //Correct the lookup array
        $counter = 1;
        foreach (array_keys(array: $lookupArray) as $entityId) {
            $lookupArray[$entityId] = $counter++;
        }

        return $lookupArray;
    }

    public static function createReorderLookupArrayWithPreviousEntity(array $entities, AbstractEntity $movedEntity, ?AbstractEntity $previousEntity): array
    {
        //Make sure we only pass in entities of the type AbstractEntity
        Assert::allIsInstanceOf(value: $entities, class: AbstractEntity::class);

        //Create an array with all values with the new sequence having increments of 10
        $lookupArray = [];
        $key         = 10;
        foreach ($entities as $entity) {
            $lookupArray[$entity->getId()] = $key;
            $key                           += 10;
        }

        //We need to find the new order, if the previous entity is null, the movedEntity will be the first
        $sequence = 1;

        if ($previousEntity instanceof \Application\Entity\AbstractEntity) {
            //Now we have a previous entity, so the sequence is 1 higher than the previous entity
            $sequence = $lookupArray[$previousEntity->getId()] + 1;
        }

        //Now we save the new sequence
        $lookupArray[$movedEntity->getId()] = $sequence;

        //Sort the array on values
        asort(array: $lookupArray);

        //Correct the lookup array
        $counter = 1;
        foreach (array_keys(array: $lookupArray) as $key) {
            $lookupArray[$key] = $counter++;
        }

        return $lookupArray;
    }
}