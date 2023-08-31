<?php

namespace App\Domain\Shared\ValueObjects;

class Clock
{
    private \DateTimeImmutable $date;
    public function __construct(\DateTimeImmutable $date)
    {
        $this->date = $date;
    }

    public function getDateTime(): \DateTimeImmutable {
        return $this->date;
    }
}