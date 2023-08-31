<?php
namespace App\Tests\Domain\Core\Entity;

use App\Domain\Core\Entity\Standard;
use App\Domain\Core\Entity\Supervisor;
use App\Domain\Shared\IdGenerator;
use PHPUnit\Framework\TestCase;

final class SupervisorTest extends TestCase
{
    private IdGenerator $idGenerator;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->idGenerator = new IdGenerator();
    }

    public function testSupervisorConstruct() {
        $id = $this->idGenerator->generate();
        $supervisor = new Supervisor($id);

        $this->assertInstanceOf(Supervisor::class, $supervisor);
    }

    public function testSupervisorAddAuthorityAndCheck() {
        $id = $this->idGenerator->generate();
        $supervisor = new Supervisor($id);
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');

        $supervisor->addAuthorityFor($standard);

        $this->assertEquals(true, $supervisor->hasAuthorityFor($standard));
    }

    public function testSupervisorCheckAuthorityWithoutAddingIt() {
        $id = $this->idGenerator->generate();
        $supervisor = new Supervisor($id);
        $standard = new Standard($this->idGenerator->generate(), 'Example standard');

        // we do not add authority
        $this->assertEquals(false, $supervisor->hasAuthorityFor($standard));
    }

}