<?php

namespace App\Tests\Domain\Core\ValueObjects;

use App\Domain\Core\Exception\RatingOutOfRangeException;
use App\Domain\Core\ObjectValue\Rating;
use App\Domain\Shared\ValueObjects\Clock;
use PHPUnit\Framework\TestCase;

class ClockTest extends TestCase
{
    public function testClockConstruct() {
        $clock = new Clock(new \DateTimeImmutable('2022-10-10'));

        $this->assertEquals('2022-10-10', $clock->getDateTime()->format('Y-m-d'));
    }
}