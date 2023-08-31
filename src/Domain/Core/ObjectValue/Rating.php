<?php

namespace App\Domain\Core\ObjectValue;

use App\Domain\Core\Exception\RatingOutOfRangeException;

/**
 * Class is needed to keep the rating in some business rules.
 * Simple integer would be non-extendable.
 * -10 <-> 10 is just an example - it was not specified in the brief.
 */
class Rating
{
    private const MIN_RATING = -10;
    private const MAX_RATING = 10;
    private int $ratingValue;

    public function __construct(int $ratingValue)
    {
        if ($ratingValue < self::MIN_RATING || $ratingValue > self::MAX_RATING) {
            throw new RatingOutOfRangeException(self::MIN_RATING, self::MAX_RATING);
        }
        $this->ratingValue = $ratingValue;
    }

    public function getValue(): int
    {
        return $this->ratingValue;
    }

    public function isPositive(): bool
    {
        return $this->ratingValue > 0;
    }
}
