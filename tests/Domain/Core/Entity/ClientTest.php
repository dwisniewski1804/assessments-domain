<?php

namespace App\Tests\Domain\Core\Entity;

use App\Domain\Core\Entity\Assessment;
use App\Domain\Core\Entity\Client;
use App\Domain\Core\Entity\Contract;
use App\Domain\Core\Entity\Standard;
use App\Domain\Core\Entity\Supervisor;
use App\Domain\Core\Exception\CanNotAddContractForOtherClientException;
use App\Domain\Shared\IdGenerator;
use App\Domain\Shared\ValueObjects\Clock;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    private IdGenerator $idGenerator;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->idGenerator = new IdGenerator();
    }

    public function testClientConstruct()
    {
        $id = $this->idGenerator->generate();

        $client = new Client($id);

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testClientAddContractFromClientShouldFail()
    {
        $id = $this->idGenerator->generate();
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $otherClient = new Client($this->idGenerator->generate());
        $contract = new Contract($id, new \DateTime(), new \DateTime(), $supervisor, $otherClient);

        $this->expectException(CanNotAddContractForOtherClientException::class);

        $client->addContract($contract);
    }

    public function testClientAddContractThatIsOwnedByHim()
    {
        $id = $this->idGenerator->generate();
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $contract = new Contract($id, new \DateTime(), new \DateTime(), $supervisor, $client);

        $client->addContract($contract);

        $this->assertEquals(1, $client->countContracts());
    }

    public function testClientHasActiveContract()
    {
        $id = $this->idGenerator->generate();
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());

        $contract = new Contract($id, new \DateTime(), new \DateTime(), $supervisor, $client);
        $client->addContract($contract);

        $this->assertEquals(true, $client->hasActiveContractWith($supervisor));
    }

    public function testClientDoesNotHaveActiveContract()
    {
        $id = $this->idGenerator->generate();
        $supervisor = new Supervisor($this->idGenerator->generate());
        $otherSupervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());

        $contract = new Contract($id, new \DateTime(), new \DateTime(), $supervisor, $client);
        $client->addContract($contract);

        $this->assertEquals(false, $client->hasActiveContractWith($otherSupervisor));
    }

    public function testClientAddAssessment()
    {
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');
        $supervisor->addAuthorityFor($standard);
        $contact = new Contract($this->idGenerator->generate(), new \DateTime(), (new \DateTime())->modify('+ 10 days'), $supervisor, $client);
        $client->addContract($contact);
        $clock = new Clock(new \DateTimeImmutable('now'));
        $assessment = new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard, $clock);

        $this->assertEquals(1, $client->countAssessments());
    }

    public function testClientAddAssessmentWithTheSameStandard()
    {
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');
        $supervisor->addAuthorityFor($standard);
        $contact = new Contract($this->idGenerator->generate(), new \DateTime(), (new \DateTime())->modify('+ 10 days'), $supervisor, $client);
        $client->addContract($contact);
        $clock = new Clock(new \DateTimeImmutable('now'));
        $assessment = new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard, $clock);
        $assessment2 = new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard, $clock);

        $this->assertEquals(1, $client->countAssessments());
    }

    public function testClientAddAssessmentWithTheOtherStandard()
    {
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');
        $standard2 = new Standard($this->idGenerator->generate(), 'Example standard2');
        $supervisor->addAuthorityFor($standard);
        $contact = new Contract($this->idGenerator->generate(), new \DateTime(), (new \DateTime())->modify('+ 10 days'), $supervisor, $client);
        $client->addContract($contact);
        $clock = new Clock(new \DateTimeImmutable('now'));
        $assessment = new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard, $clock);
        $assessment2 = new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard2, $clock);

        $this->assertEquals(2, $client->countAssessments());
    }
}
