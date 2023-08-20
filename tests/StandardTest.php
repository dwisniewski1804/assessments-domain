<?php
namespace App\Tests;

use App\Domain\Core\Entity\Standard;
use PHPUnit\Framework\TestCase;

final class StandardTest extends TestCase
{
    public function testTeamConstructor() {
        $team = new Standard('Brasil');
        $this->assertInstanceOf(Team::class, $team);
        $this->assertEquals('Brasil', $team->name);
    }

}