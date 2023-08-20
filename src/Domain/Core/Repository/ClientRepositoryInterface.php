<?php

namespace App\Domain\Core\Repository;

use App\Domain\Core\Entity\Client;
use Symfony\Component\Uid\Uuid;

interface ClientRepositoryInterface
{
    public function save(Client $client): Client;

    public function findOneById(UUID $id): ?Client;
}