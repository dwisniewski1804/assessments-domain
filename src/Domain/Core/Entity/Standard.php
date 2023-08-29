<?php

namespace App\Domain\Core\Entity;

use App\Domain\Shared\ValueObjects\Uuid;

class Standard
{
    public readonly Uuid $id;
    private string $name;

    public function __construct(Uuid $id, string $name) {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

}