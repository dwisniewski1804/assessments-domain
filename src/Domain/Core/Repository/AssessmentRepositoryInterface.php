<?php

namespace App\Domain\Core\Repository;

use App\Domain\Core\Entity\Assessment;
use Symfony\Component\Uid\Uuid;

interface AssessmentRepositoryInterface
{
    public function save(Assessment $assessment): Assessment;

    public function findOneById(UUID $id): ?Assessment;
}