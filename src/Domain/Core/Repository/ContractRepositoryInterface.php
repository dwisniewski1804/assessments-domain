<?php

namespace App\Domain\Core\Repository;

use App\Domain\Core\Entity\Contract;
use App\Domain\Shared\ValueObjects\Uuid;

interface ContractRepositoryInterface
{
    public function save(Contract $supervisor): Contract;

    public function findOneById(Uuid $id): ?Contract;
}