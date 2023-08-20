<?php

namespace App\Domain\Core\Repository;

use App\Domain\Core\Entity\Supervisor;
use Symfony\Component\Uid\Uuid;

interface SupervisorRepositoryInterface
{
    public function save(Supervisor $supervisor): Supervisor;

    public function findOneById(UUID $id): ?Supervisor;
}