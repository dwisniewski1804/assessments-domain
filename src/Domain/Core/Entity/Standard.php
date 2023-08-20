<?php

namespace App\Domain\Core\Entity;

class Standard
{
    public readonly string $id;
    private string $name;

    public function __construct(string $id, string $name) {
        $this->id = $id;
        $this->name = $name;
    }
    public function getName(): string
    {
        return $this->name;
    }

}