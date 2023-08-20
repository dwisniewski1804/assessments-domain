<?php

namespace App\Domain\Core\Entity;

class Client
{
    private array $assessments;
    private array $contracts;

    public function hasActiveContractWith(Supervisor $supervisor) {
        /**
         * @var Contract $contract
         */
        // if we had collection object here we could use filter/find function
        foreach ($this->contracts as $contract) {
            if ($contract->supervisor === $supervisor) {
                return true;
            }
        }
        return false;
    }

    public function hasActiveAssessmentFor(Standard $standard): bool {
        // if we had collection object here we could use filter/find function
        foreach ($this->assessments as $assessment) {
            if ($assessment->standard === $standard && $assessment->isExpired()) {
                return true;
            }
        }

        return false;
    }

    public function addAssessment(Assessment $assessment) {
        $this->assessments[] = $assessment;
    }
}