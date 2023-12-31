<?php

namespace App\Domain\Core\Entity;

use App\Domain\Core\Exception\CanNotAddContractForOtherClientException;
use App\Domain\Shared\ValueObjects\Uuid;

class Client
{
    private readonly Uuid $id;
    private array $assessments;
    private array $contracts;

    public function __construct(Uuid $id)
    {
        $this->id = $id;
        $this->contracts = [];
        $this->assessments = [];
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function addContract(Contract $contract)
    {
        if ($contract->client->getId() !== $this->id) {
            throw new CanNotAddContractForOtherClientException();
        }
        $this->contracts[] = $contract;
    }

    public function hasActiveContractWith(Supervisor $supervisor): bool
    {
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

    public function addAssessment(Assessment $assessment): self
    {
        $this->assessments[$assessment->getStandard()->getId()->__toString()] = $assessment;

        return $this;
    }

    public function countAssessments(): int
    {
        return count($this->assessments);
    }

    public function countContracts(): int
    {
        return count($this->contracts);
    }
}
