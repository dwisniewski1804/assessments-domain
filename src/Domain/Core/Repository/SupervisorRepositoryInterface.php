<?php

namespace App\Domain\Core\Repository;

use App\Domain\Core\Entity\Supervisor;
use App\Domain\Shared\ValueObjects\Uuid;

interface SupervisorRepositoryInterface
{
    public function save(Supervisor $supervisor): Supervisor;

    public function findOneById(Uuid $id): ?Supervisor;
}