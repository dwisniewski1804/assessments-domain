<?php

namespace App\Tests\Domain\Core\Entity;

use App\Domain\Core\Entity\Standard;
use App\Domain\Shared\IdGenerator;
use PHPUnit\Framework\TestCase;

final class StandardTest extends TestCase
{
    private IdGenerator $idGenerator;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->idGenerator = new IdGenerator();
    }

    public function testStandardConstruct()
    {
        $id = $this->idGenerator->generate();
        $standard = new Standard($id, 'Example standard');

        $this->assertInstanceOf(Standard::class, $standard);
    }

}
