<?php

namespace App\Domain\Core\Entity;

use App\Domain\Core\Entity\Enum\LockType;
use App\Domain\Core\Exception\AssessmentAlreadyLockedException;
use App\Domain\Core\Exception\CanNotEvaluateDueToTimeAfterRulesException;
use App\Domain\Core\Exception\ClientDoesNotHaveActiveContractWithSupervisorException;
use App\Domain\Core\Exception\ExpiredAssessmentCanNotBeLockedException;
use App\Domain\Core\Exception\SupervisorDoesNotHaveAuthorityException;
use App\Domain\Core\Exception\WithdrawnedAssessmentCanNotBeUnlockedException;
use App\Domain\Core\ObjectValue\Lock;
use App\Domain\Core\ObjectValue\Rating;
use App\Domain\Shared\ValueObjects\Uuid;
use DateTime;

class Assessment
{
    private readonly Uuid $id;
    private Supervisor $supervisor;
    private Client $client;
    private Standard $standard;
    private Rating $rating;
    private readonly \DateTime $date;

    // TODO instead lock property we should have separated model for LockedAssessment or even WithdrawnAssessment and SuspendedAssessment classes
    private ?Lock $lock;

    const EXPIRATION_DAYS = 365;

    public function __construct(
        Uuid $id,
        Supervisor $supervisor,
        Client $client,
        Standard $standard
    ) {
        if (!$client->hasActiveContractWith($supervisor)) {
            throw new ClientDoesNotHaveActiveContractWithSupervisorException;
        }
        $this->id = $id;
        $this->supervisor = $supervisor;
        $this->client = $client;
        $this->standard = $standard;
        $this->date = new DateTime();
    }

    public function lock(LockType $type, string $description): self {

        if($this->lock) {
            throw new AssessmentAlreadyLockedException;
        }

        if ($this->isExpired()) {
            throw new ExpiredAssessmentCanNotBeLockedException;
        }

        $lock = new Lock($type, $description);
        $this->lock = $lock;

        return $this;
    }

    public function unlock() {
       if ($this->lock->getType() === LockType::WITHDRAWN) {
           throw new WithdrawnedAssessmentCanNotBeUnlockedException();
       }

       $this->lock = null;
    }

    public function evaluate(Rating $rating) {

        if ($this->canEvaluateAfter() > new \DateTime()) {
            throw new CanNotEvaluateDueToTimeAfterRulesException;
        }

        if (!$this->supervisor->hasAuthorityFor($this->standard)) {
            throw new SupervisorDoesNotHaveAuthorityException;
        }

        $this->rating = $rating;
        $this->client->addAssessment($this);
    }

    private function canEvaluateAfter() {
        if (isset($this->rating)) {
            if ($this->rating->isPositive()) {
                return (clone $this->date)->modify('+180 days');
            }
            return (clone $this->date)->modify('+30 days');
        }

        return new \DateTime();
    }

    private function isExpired(): bool {
        $expirationDate = clone $this->date;
        $expirationDate->modify('+' . self::EXPIRATION_DAYS . ' days');
        return (new DateTime()) > $expirationDate;
    }

    public function getSupervisor(): Supervisor
    {
        return $this->supervisor;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getStandard(): Standard
    {
        return $this->standard;
    }

    public function getRating(): Rating
    {
        return $this->rating;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getLock(): ?Lock
    {
        return $this->lock;
    }
}