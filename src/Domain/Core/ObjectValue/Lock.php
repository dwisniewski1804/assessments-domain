<?php

namespace App\Domain\Core\ObjectValue;

use App\Domain\Core\Entity\Enum\LockType;

class Lock
{
    private LockType $type;
    private string $description;

    public function __construct(LockType $type, string $description) {
        $this->type = $type;
        $this->description = $description;
    }

    public function getType(): LockType
    {
        return $this->type;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}