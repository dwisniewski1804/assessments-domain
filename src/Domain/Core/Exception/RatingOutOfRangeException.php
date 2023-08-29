<?php

namespace App\Domain\Core\Exception;

use App\Domain\Shared\Exception\DomainException;

class RatingOutOfRangeException extends DomainException
{
    protected $message = "Rating is out of range";

    public function __construct(int $min, int $max)
    {
        parent::__construct($this->message);
    }
}