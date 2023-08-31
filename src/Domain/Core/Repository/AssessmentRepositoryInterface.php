<?php

namespace App\Domain\Core\Repository;

use App\Domain\Core\Entity\Assessment;
use App\Domain\Shared\ValueObjects\Uuid;

interface AssessmentRepositoryInterface
{
    public function save(Assessment $assessment): Assessment;

    public function findOneById(Uuid $id): ?Assessment;
}
