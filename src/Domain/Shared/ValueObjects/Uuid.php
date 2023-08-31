<?php

namespace App\Domain\Shared\ValueObjects;

class Uuid
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
