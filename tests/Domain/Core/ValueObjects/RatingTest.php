<?php

namespace App\Tests\Domain\Core\ValueObjects;

use App\Domain\Core\Exception\RatingOutOfRangeException;
use App\Domain\Core\ObjectValue\Rating;
use PHPUnit\Framework\TestCase;

class RatingTest extends TestCase
{
    public function testRatingConstruct() {
        $rating = new Rating(5);
        $this->assertEquals(5, $rating->getValue());
    }

    public function testRatingConstructWithLowerValue() {
        $this->expectException(RatingOutOfRangeException::class);
        new Rating(-15);
    }

    public function testRatingConstructWithHigherValue() {
        $this->expectException(RatingOutOfRangeException::class);
        new Rating(15);
    }

    public function testRatingPositive() {
        $rating = new Rating(5);
        $this->assertEquals(true, $rating->isPositive());
    }

    public function testRatingNotPositive() {
        $rating = new Rating(-5);
        $this->assertEquals(false, $rating->isPositive());
    }
}