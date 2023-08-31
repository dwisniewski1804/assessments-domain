<?php

namespace App\Domain\Core\Repository;

use App\Domain\Core\Entity\Client;
use App\Domain\Shared\ValueObjects\Uuid;

interface ClientRepositoryInterface
{
    public function save(Client $client): Client;

    public function findOneById(Uuid $id): ?Client;
}