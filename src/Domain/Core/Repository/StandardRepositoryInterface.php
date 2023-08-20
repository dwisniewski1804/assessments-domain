<?php

namespace App\Domain\Core\Repository;

use App\Domain\Core\Entity\Standard;
use Symfony\Component\Uid\Uuid;

interface StandardRepositoryInterface
{
    public function save(Standard $standard): Standard;

    public function findOneById(UUID $id): ?Standard;
}