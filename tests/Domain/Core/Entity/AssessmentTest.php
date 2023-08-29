<?php
namespace App\Tests\Domain\Core\Entity;

use App\Domain\Core\Entity\Assessment;
use App\Domain\Core\Entity\Client;
use App\Domain\Core\Entity\Contract;
use App\Domain\Core\Entity\Standard;
use App\Domain\Core\Entity\Supervisor;
use App\Domain\Core\Exception\ClientDoesNotHaveActiveContractWithSupervisorException;
use App\Domain\Core\ObjectValue\Rating;
use App\Domain\Shared\IdGenerator;
use PHPUnit\Framework\TestCase;

final class AssessmentTest extends TestCase
{
    private IdGenerator $idGenerator;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->idGenerator = new IdGenerator();
    }

    public function testAssessmentConstruct() {
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');
        $contact = new Contract($this->idGenerator->generate(), new \DateTime(), (new \DateTime())->modify('+ 10 days'), $supervisor, $client);
        $client->addContract($contact);
        $assessment = new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard);

        $this->assertInstanceOf(Assessment::class, $assessment);
    }

    public function testAssessmentConstructWithUnactiveContract() {
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');

        $this->expectException(ClientDoesNotHaveActiveContractWithSupervisorException::class);

        new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard);
    }

    public function testAssessmentEvaluate() {
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');
        $supervisor->addAuthorityFor($standard);
        $contact = new Contract($this->idGenerator->generate(), new \DateTime(), (new \DateTime())->modify('+ 10 days'), $supervisor, $client);
        $client->addContract($contact);
        $assessment = new Assessment($this->idGenerator->generate(), $supervisor, $client, $standard);
        $assessment->evaluate(new Rating(5));

        $this->assertEquals($client->countAssessments(), 1);
    }
}