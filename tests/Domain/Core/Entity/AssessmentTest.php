<?php

namespace App\Tests\Domain\Core\Entity;

use App\Domain\Core\Entity\Assessment;
use App\Domain\Core\Entity\Client;
use App\Domain\Core\Entity\Contract;
use App\Domain\Core\Entity\Enum\LockType;
use App\Domain\Core\Entity\Standard;
use App\Domain\Core\Entity\Supervisor;
use App\Domain\Core\Exception\AssessmentAlreadyLockedException;
use App\Domain\Core\Exception\CanNotEvaluateDueToTimeAfterRulesException;
use App\Domain\Core\Exception\ClientDoesNotHaveActiveContractWithSupervisorException;
use App\Domain\Core\Exception\ExpiredAssessmentCanNotBeLockedException;
use App\Domain\Core\Exception\WithdrawnAssessmentCanNotBeUnlockedException;
use App\Domain\Core\Exception\WithdrawnAssessmentCanNotLockedException;
use App\Domain\Core\ObjectValue\Lock;
use App\Domain\Core\ObjectValue\Rating;
use App\Domain\Shared\IdGenerator;
use App\Domain\Shared\ValueObjects\Clock;
use PHPUnit\Framework\TestCase;

final class AssessmentTest extends TestCase
{
    private IdGenerator $idGenerator;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->idGenerator = new IdGenerator();
    }

    public function testAssessmentConstruct()
    {
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');
        $contact = new Contract($this->idGenerator->generate(), new \DateTime(), (new \DateTime())->modify('+ 10 days'), $supervisor, $client);
        $client->addContract($contact);
        $clock = new Clock(new \DateTimeImmutable('now'));
        $assessment = new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard, $clock);

        $this->assertInstanceOf(Assessment::class, $assessment);
    }

    public function testAssessmentConstructWithUnactiveContractShouldFail()
    {
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');

        $this->expectException(ClientDoesNotHaveActiveContractWithSupervisorException::class);
        $clock = new Clock(new \DateTimeImmutable('now'));
        new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard, $clock);
    }

    public function testAssessmentEvaluate()
    {
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');
        $supervisor->addAuthorityFor($standard);
        $contact = new Contract($this->idGenerator->generate(), new \DateTime(), (new \DateTime())->modify('+ 10 days'), $supervisor, $client);
        $client->addContract($contact);
        $clock = new Clock(new \DateTimeImmutable('now'));
        $assessment = new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard, $clock);
        $assessment->evaluate(new Rating(5));

        $this->assertEquals($client->countAssessments(), 1);
    }

    public function testAssessmentExpirationExceeded()
    {
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');
        $supervisor->addAuthorityFor($standard);
        $contact = new Contract($this->idGenerator->generate(), new \DateTime(), (new \DateTime())->modify('+ 10 days'), $supervisor, $client);
        $client->addContract($contact);
        $clock = new Clock(new \DateTimeImmutable('-400 days'));
        $assessment = new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard, $clock);

        $this->assertEquals(true, $assessment->isExpired());
    }

    public function testAssessmentExpirationNotExceeded()
    {
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');
        $supervisor->addAuthorityFor($standard);
        $contact = new Contract($this->idGenerator->generate(), new \DateTime(), (new \DateTime())->modify('+ 10 days'), $supervisor, $client);
        $client->addContract($contact);
        $clock = new Clock(new \DateTimeImmutable('now'));
        $assessment = new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard, $clock);

        $this->assertEquals(false, $assessment->isExpired());
    }
    public function testAssessmentSuspend()
    {
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');
        $supervisor->addAuthorityFor($standard);
        $contact = new Contract($this->idGenerator->generate(), new \DateTime(), (new \DateTime())->modify('+ 10 days'), $supervisor, $client);
        $client->addContract($contact);
        $clock = new Clock(new \DateTimeImmutable('now'));
        $assessment = new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard, $clock);
        $assessment->evaluate(new Rating(5));
        $assessment->lock(LockType::SUSPENDED, "The reason for suspension.");

        $this->assertEquals(LockType::SUSPENDED, $assessment->getLock()->getType());
        $this->assertEquals("The reason for suspension.", $assessment->getLock()->getDescription());
    }

    public function testAssessmentSuspendAgainShouldFail()
    {
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');
        $supervisor->addAuthorityFor($standard);
        $contact = new Contract($this->idGenerator->generate(), new \DateTime(), (new \DateTime())->modify('+ 10 days'), $supervisor, $client);
        $client->addContract($contact);
        $clock = new Clock(new \DateTimeImmutable('now'));
        $assessment = new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard, $clock);
        $assessment->evaluate(new Rating(5));
        $assessment->lock(LockType::SUSPENDED, "The reason for suspension.");

        $this->expectException(AssessmentAlreadyLockedException::class);

        $assessment->lock(LockType::SUSPENDED, "The second reason for suspension.");
    }

    public function testAssessmentSuspendAndThenWithdraw()
    {
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');
        $supervisor->addAuthorityFor($standard);
        $contact = new Contract($this->idGenerator->generate(), new \DateTime(), (new \DateTime())->modify('+ 10 days'), $supervisor, $client);
        $client->addContract($contact);
        $clock = new Clock(new \DateTimeImmutable('now'));
        $assessment = new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard, $clock);
        $assessment->evaluate(new Rating(5));

        $assessment->lock(LockType::SUSPENDED, "The reason for suspension.");
        $assessment->lock(LockType::WITHDRAWN, "The reason for widthdraw.");

        $this->assertEquals(LockType::WITHDRAWN, $assessment->getLock()->getType());
    }

    public function testAssessmentWithdrawAndThenSuspendShouldFail()
    {
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');
        $supervisor->addAuthorityFor($standard);
        $contact = new Contract($this->idGenerator->generate(), new \DateTime(), (new \DateTime())->modify('+ 10 days'), $supervisor, $client);
        $client->addContract($contact);
        $clock = new Clock(new \DateTimeImmutable('now'));
        $assessment = new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard, $clock);
        $assessment->evaluate(new Rating(5));
        $assessment->lock(LockType::WITHDRAWN, "The reason for widthdraw.");

        $this->expectException(WithdrawnAssessmentCanNotLockedException::class);

        $assessment->lock(LockType::SUSPENDED, "The reason for suspension.");
    }

    public function testAssessmentWithdrawAndThenUnlockShouldFail()
    {
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');
        $supervisor->addAuthorityFor($standard);
        $contact = new Contract($this->idGenerator->generate(), new \DateTime(), (new \DateTime())->modify('+ 10 days'), $supervisor, $client);
        $client->addContract($contact);
        $clock = new Clock(new \DateTimeImmutable('now'));
        $assessment = new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard, $clock);
        $assessment->evaluate(new Rating(5));
        $assessment->lock(LockType::WITHDRAWN, "The reason for widthdraw.");

        $this->expectException(WithdrawnAssessmentCanNotBeUnlockedException::class);

        $assessment->unlock();
    }

    public function testAssessmentWithdrawAndThenUnlockAndThenLock()
    {
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');
        $supervisor->addAuthorityFor($standard);
        $contact = new Contract($this->idGenerator->generate(), new \DateTime(), (new \DateTime())->modify('+ 10 days'), $supervisor, $client);
        $client->addContract($contact);
        $clock = new Clock(new \DateTimeImmutable('now'));
        $assessment = new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard, $clock);
        $assessment->evaluate(new Rating(5));
        $assessment->lock(LockType::SUSPENDED, "The reason for widthdraw.");
        $assessment->unlock();
        $assessment->lock(LockType::SUSPENDED, "The reason for widthdraw.");
        $this->assertEquals(LockType::SUSPENDED, $assessment->getLock()->getType());

    }

    public function testExpiredAssessmentLockShouldFail()
    {
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');
        $supervisor->addAuthorityFor($standard);
        $contact = new Contract($this->idGenerator->generate(), new \DateTime(), (new \DateTime())->modify('+ 10 days'), $supervisor, $client);
        $client->addContract($contact);
        $clock = new Clock(new \DateTimeImmutable('-400 days'));
        $assessment = new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard, $clock);
        $assessment->evaluate(new Rating(5));

        $this->expectException(ExpiredAssessmentCanNotBeLockedException::class);
        $assessment->lock(LockType::WITHDRAWN, "The reason for widthdraw.");
    }

    public function testAssessmentLockWithoutDescriptionShouldFail()
    {
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');
        $supervisor->addAuthorityFor($standard);
        $contact = new Contract($this->idGenerator->generate(), new \DateTime(), (new \DateTime())->modify('+ 10 days'), $supervisor, $client);
        $client->addContract($contact);
        $clock = new Clock(new \DateTimeImmutable('-400 days'));
        $assessment = new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard, $clock);
        $assessment->evaluate(new Rating(5));

        $this->expectException(\ArgumentCountError::class);
        $assessment->lock(LockType::WITHDRAWN);
    }

    public function testAssessmentEvaluatePositiveAgainIn220Days()
    {
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');
        $supervisor->addAuthorityFor($standard);
        $contact = new Contract($this->idGenerator->generate(), new \DateTime(), (new \DateTime())->modify('+ 10 days'), $supervisor, $client);
        $client->addContract($contact);
        $clock = new Clock(new \DateTimeImmutable('-220 days'));
        $assessment = new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard, $clock);
        $assessment->evaluate(new Rating(5));
        $assessment->evaluate(new Rating(3));

        $this->assertInstanceOf(Assessment::class, $assessment);
    }

    public function testAssessmentEvaluatePositiveAgainIn170DaysShouldFail()
    {
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');
        $supervisor->addAuthorityFor($standard);
        $contact = new Contract($this->idGenerator->generate(), new \DateTime(), (new \DateTime())->modify('+ 10 days'), $supervisor, $client);
        $client->addContract($contact);
        $clock = new Clock(new \DateTimeImmutable('-170 days'));
        $assessment = new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard, $clock);
        $assessment->evaluate(new Rating(5));

        $this->expectException(CanNotEvaluateDueToTimeAfterRulesException::class);

        $assessment->evaluate(new Rating(3));
    }

    public function testAssessmentEvaluateNegativeAgainIn32Days()
    {
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');
        $supervisor->addAuthorityFor($standard);
        $contact = new Contract($this->idGenerator->generate(), new \DateTime(), (new \DateTime())->modify('+ 10 days'), $supervisor, $client);
        $client->addContract($contact);
        $clock = new Clock(new \DateTimeImmutable('-32 days'));
        $assessment = new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard, $clock);
        $assessment->evaluate(new Rating(-5));
        $assessment->evaluate(new Rating(3));

        $this->assertInstanceOf(Assessment::class, $assessment);
    }

    public function testAssessmentEvaluateNegativeAgainIn29DaysShouldFail()
    {
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');
        $supervisor->addAuthorityFor($standard);
        $contact = new Contract($this->idGenerator->generate(), new \DateTime(), (new \DateTime())->modify('+ 10 days'), $supervisor, $client);
        $client->addContract($contact);
        $clock = new Clock(new \DateTimeImmutable('-29 days'));
        $assessment = new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard, $clock);
        $assessment->evaluate(new Rating(-5));

        $this->expectException(CanNotEvaluateDueToTimeAfterRulesException::class);

        $assessment->evaluate(new Rating(3));
    }
}
