<?php
namespace App\Tests\Domain\Core\Entity;

use App\Domain\Core\Entity\Standard;
use PHPUnit\Framework\TestCase;

final class StandardTest extends TestCase
{
    public function testTeamConstructor() {
        $team = new Standard();
    }

}