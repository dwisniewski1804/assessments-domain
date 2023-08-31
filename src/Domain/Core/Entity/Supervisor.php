<?php

namespace App\Domain\Core\Entity;

use App\Domain\Shared\ValueObjects\Uuid;

class Supervisor
{
    private readonly Uuid $id;
    private array $authorities = []; // Standards for which supervisor has authority

    public function __construct(Uuid $id)
    {
        $this->id = $id;
    }

    public function hasAuthorityFor(Standard $standard): bool {
        return array_key_exists($standard->id->__toString(), $this->authorities);
    }

    public function addAuthorityFor(Standard $standard): self
    {
        $this->authorities[$standard->getId()->__toString()] = $standard;

        return $this;
    }

    public function removeAuthorityFor(Standard $standard): self
    {
        unset($this->authorities[$standard->getId()->__toString()]);
        return $this;
    }
}