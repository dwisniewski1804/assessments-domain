<?php

namespace App\Domain\Core\Entity;

use App\Domain\Core\Exception\ContractNotFoundException;
use App\Domain\Core\Exception\SupervisorDoesNotHaveAuthorityException;
use Exception;
use Symfony\Component\Uid\Uuid;

class Supervisor
{
    private array $authorities = []; // Standards for which supervisor has authority

    public function hasAuthorityFor(Standard $standard) {
        return in_array($standard->id, $this->authorities);
    }

    public function evaluate(Uuid $id, Client $client, Standard $standard, int $rating) {
        if (!$client->hasActiveContractWith($this)) {
            throw new ContractNotFoundException;
        }

        if (!$this->hasAuthorityFor($standard)) {
            throw new SupervisorDoesNotHaveAuthorityException;
        }

        $assessment = new Assessment($id, $this, $client, $standard, $rating);
        $client->addAssessment($assessment);

        return $assessment;
    }

}