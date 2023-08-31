<?php
namespace App\Tests\Domain\Core\Entity;

use App\Domain\Core\Entity\Client;
use App\Domain\Core\Entity\Contract;
use App\Domain\Core\Entity\Standard;
use App\Domain\Core\Entity\Supervisor;
use App\Domain\Shared\IdGenerator;
use PHPUnit\Framework\TestCase;

final class ContractTest extends TestCase
{
    private IdGenerator $idGenerator;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->idGenerator = new IdGenerator();
    }

    public function testContractConstruct() {
        $id = $this->idGenerator->generate();
        $supervisor = new Supervisor($this->idGenerator->generate());
        $client = new Client($this->idGenerator->generate());
        $contract = new Contract($id, new \DateTime(), new \DateTime(), $supervisor, $client);

        $this->assertInstanceOf(Contract::class, $contract);
    }

}