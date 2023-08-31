<?php

namespace App\Domain\Core\Entity;

use App\Domain\Core\Entity\Enum\LockType;
use App\Domain\Core\Exception\AssessmentAlreadyLockedException;
use App\Domain\Core\Exception\CanNotEvaluateDueToTimeAfterRulesException;
use App\Domain\Core\Exception\ClientDoesNotHaveActiveContractWithSupervisorException;
use App\Domain\Core\Exception\ExpiredAssessmentCanNotBeLockedException;
use App\Domain\Core\Exception\SupervisorDoesNotHaveAuthorityException;
use App\Domain\Core\Exception\WithdrawnAssessmentCanNotBeUnlockedException;
use App\Domain\Core\Exception\WithdrawnAssessmentCanNotLockedException;
use App\Domain\Core\ObjectValue\Lock;
use App\Domain\Core\ObjectValue\Rating;
use App\Domain\Shared\ValueObjects\Clock;
use App\Domain\Shared\ValueObjects\Uuid;
use DateTime;

class Assessment
{
    private readonly Uuid $id;
    private Supervisor $supervisor;
    private Client $client;
    private Standard $standard;
    private Rating $rating;
    private readonly \DateTimeImmutable $date;

    // TODO instead lock property we should have separated model for LockedAssessment or even WithdrawnAssessment and SuspendedAssessment classes
    private ?Lock $lock = null;

    const EXPIRATION_DAYS = 365;
    private const NEGATIVE_DAYS = 30;
    private const POSITIVE_DAYS = 180;
    public function __construct(
        Uuid $id,
        Supervisor $supervisor,
        Client $client,
        Standard $standard,
        Clock $date
    ) {
        if (!$client->hasActiveContractWith($supervisor)) {
            throw new ClientDoesNotHaveActiveContractWithSupervisorException;
        }
        $this->id = $id;
        $this->supervisor = $supervisor;
        $this->client = $client;
        $this->standard = $standard;
        $this->date = $date->getDateTime();
    }

    public function lock(LockType $type, string $description): self {
        $lock = new Lock($type, $description);

        if ($type === LockType::SUSPENDED && $this->lock && $this->lock->getType() === LockType::SUSPENDED) {
            throw new AssessmentAlreadyLockedException;
        }

        if ($this->lock && $this->lock->getType() === LockType::WITHDRAWN) {
            throw new WithdrawnAssessmentCanNotLockedException();
        }

        if ($this->isExpired()) {
            throw new ExpiredAssessmentCanNotBeLockedException;
        }

        $this->lock = $lock;

        return $this;
    }

    public function unlock() {
       if ($this->lock->getType() === LockType::WITHDRAWN) {
           throw new WithdrawnAssessmentCanNotBeUnlockedException();
       }

       $this->lock = null;
    }

    public function evaluate(Rating $rating) {

        if ($this->canEvaluateAfter() > new \DateTime()) {
            throw new CanNotEvaluateDueToTimeAfterRulesException(self::NEGATIVE_DAYS, self::POSITIVE_DAYS);
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
                return (clone $this->date)->modify('+' . self::POSITIVE_DAYS . ' days');
            }
            return (clone $this->date)->modify('+'. self::NEGATIVE_DAYS .' days');
        }

        return new \DateTime();
    }

    public function isExpired(): bool {
        $expirationDate = new \DateTime($this->date->format('Y-m-d H:i:s'));
        $expirationDate->modify('+' . self::EXPIRATION_DAYS . ' days');

        return (new \DateTimeImmutable()) > $expirationDate;
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

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getLock(): ?Lock
    {
        return $this->lock;
    }
}