<?php

declare(strict_types=1);

namespace Application\Entity;

interface EntityInterface
{
    public function getId();

    public function setId(int $id);
}
