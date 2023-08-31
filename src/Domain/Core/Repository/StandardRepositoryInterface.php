<?php

namespace App\Domain\Core\Repository;

use App\Domain\Core\Entity\Standard;
use App\Domain\Shared\ValueObjects\Uuid;

interface StandardRepositoryInterface
{
    public function save(Standard $standard): Standard;

    public function findOneById(Uuid $id): ?Standard;
}